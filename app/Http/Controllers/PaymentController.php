<?php

namespace App\Http\Controllers;

use App\Models\ClassEnrollment;
use App\Models\Commission;
use App\Models\CourseClass;
use App\Models\Payment;
use App\Models\Setting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Session;
use Midtrans\Config;
use Midtrans\Notification;
use Midtrans\Snap;
use Exception;

class PaymentController extends Controller
{
    public function initiatePayment(Request $request)
    {
        // 1. Validasi Input
        $request->validate([
            'course_class_id' => ['required', 'exists:course_classes,id'],
            'course_id' => ['required', 'exists:courses,id'],
        ]);
        // Tambahkan di awal logika setelah validasi input
        $class = CourseClass::with('course')->findOrFail($request->course_class_id);
        $course = $class->course;
        $user = Auth::user();

        // ✅ CEK 1: Apakah kelas sudah ditutup?
        if ($class->status !== 'open') {
            return back()->with('error', 'Kelas yang Anda pilih sudah ditutup.');
        }

        // ✅ CEK 2: Apakah masa pendaftaran sudah berakhir?
        // if ($class->enrollment_end && now()->greaterThan($class->enrollment_end)) {
        //     return back()->with('error', 'Pendaftaran untuk kelas ini telah ditutup pada ' . $class->enrollment_end->translatedFormat('d F Y, H:i') . '.');
        // }

        if ($class->enrollment_end) {
            $enrollmentEndJakarta = \Carbon\Carbon::parse($class->enrollment_end)->setTimezone('Asia/Jakarta');
            $nowJakarta = now()->setTimezone('Asia/Jakarta');

            if ($nowJakarta->greaterThan($enrollmentEndJakarta)) {
                return back()->with('error', 'Pendaftaran untuk kelas ini telah ditutup pada ' . $enrollmentEndJakarta->translatedFormat('d F Y, H:i') . '.');
            }
        }

        // ✅ CEK 3: Apakah pendaftaran belum dibuka? (opsional, tapi direkomendasikan)
        // if ($class->enrollment_start && now()->lessThan($class->enrollment_start)) {
        //     return back()->with('error', 'Pendaftaran untuk kelas ini belum dibuka. Akan dibuka pada ' . $class->enrollment_start->translatedFormat('d F Y, H:i') . '.');
        // }

        // ✅ CEK 3: Bandingkan dalam timezone Jakarta
        if ($class->enrollment_start) {
            $enrollmentStartJakarta = \Carbon\Carbon::parse($class->enrollment_start)->setTimezone('Asia/Jakarta');
            $nowJakarta = now()->setTimezone('Asia/Jakarta');

            if ($nowJakarta->lessThan($enrollmentStartJakarta)) {
                return back()->with('error', 'Pendaftaran untuk kelas ini belum dibuka. Akan dibuka pada ' . $enrollmentStartJakarta->translatedFormat('d F Y, H:i') . '.');
            }
        }
        // -----
        try {
            $class = CourseClass::with('course')->findOrFail($request->course_class_id);
            $course = $class->course;
            $user = Auth::user();  // Pastikan user sudah login

            // 2. Cek Status & Enrollment (Optional: Cek kuota, apakah sudah terdaftar, dll.)
            if ($class->status !== 'open') {
                return back()->with('error', 'Kelas yang Anda pilih sudah ditutup.');
            }

            // 3. Hitung Harga Final
            $originalPrice = $course->price;
            $finalPrice = $originalPrice;
            $discountAmount = 0;

            // Logika Diskon
            $isDiscountActive = $course->discount_price !== null &&
                ($course->discount_end_date === null || now()->lessThan($course->discount_end_date));

            if ($isDiscountActive) {
                $finalPrice = $course->discount_price;
                $discountAmount = $originalPrice - $finalPrice;
            }

            // 4. Integrasi Midtrans (REAL API CALL)
            $orderId = 'TRX-' . time() . '-' . $class->id;

            // Setup Midtrans configuration
            \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
            \Midtrans\Config::$isProduction = config('services.midtrans.is_production');
            \Midtrans\Config::$isSanitized = true;
            \Midtrans\Config::$is3ds = true;

            $params = [
                'transaction_details' => [
                    'order_id' => $orderId,
                    'gross_amount' => (int) $finalPrice,
                ],
                'item_details' => [
                    [
                        'id' => $class->id,
                        'price' => (int) $finalPrice,
                        'quantity' => 1,
                        'name' => $course->name . ' - Kelas ' . $class->name,
                    ]
                ],
                'customer_details' => [
                    'first_name' => $user->name,
                    'email' => $user->email,
                ],
            ];

            $snapToken = \Midtrans\Snap::getSnapToken($params);

            $payment = new Payment();
            $payment->course_id = $course->id;
            $payment->student_id = $user->id;
            $payment->course_class_id = $class->id;
            $payment->midtrans_order_id = $orderId;
            $payment->gross_amount = $finalPrice;
            $payment->transaction_status = 'pending';
            $payment->payment_type = 'transfer';
            $payment->save();

            // Simpan course_class_id ke sesi untuk redirect setelah bayar
            Session::put('post_payment_redirect_class_id', $request->course_class_id);
            return view('student.payment.payment', compact(
                'class',
                'course',
                'user',
                'originalPrice',
                'finalPrice',
                'discountAmount',
                'snapToken'
            ));
        } catch (Exception $e) {
            return back()->with('error', 'Gagal memproses pembayaran: ' . $e->getMessage());
        }
    }

    public function handleMidtransNotification(Request $request)
    {
        \Midtrans\Config::$serverKey = config('services.midtrans.server_key');
        \Midtrans\Config::$isProduction = config('services.midtrans.is_production');

        try {
            // 1. Ambil notifikasi dari Midtrans (otomatis verifikasi signature)
            $notif = new Notification();

            $orderId = $notif->order_id;
            $transactionStatus = $notif->transaction_status;
            $fraudStatus = $notif->fraud_status;
            $paymentType = $notif->payment_type ?? 'unknown';
            $payload = json_encode($notif->getResponse());

            // 2. Cari pembayaran berdasarkan midtrans_order_id
            $payment = Payment::where('midtrans_order_id', $orderId)->first();

            if (!$payment) {
                Log::warning("Midtrans notification: Payment not found for order_id: $orderId");
                return response('OK', 200);
            }

            // 3. Hindari pemrosesan ulang jika status tidak berubah
            if ($payment->transaction_status === $transactionStatus) {
                return response('OK', 200);
            }

            // 4. Siapkan data untuk update
            $updateData = [
                'transaction_status' => $transactionStatus,
                'payment_type' => $paymentType,
                'fraud_status' => $fraudStatus,
                'payment_payload' => $payload,
            ];

            // Update timestamp hanya jika transaksi sukses
            if (in_array($transactionStatus, ['settlement', 'capture'])) {
                $updateData['settlement_at'] = now();
                $updateData['verified_at'] = now();
            }

            // Simpan perubahan ke payment
            $payment->update($updateData);

            // 5. Tentukan apakah perlu buat ClassEnrollment
            $shouldEnroll = false;

            if ($transactionStatus === 'capture' && $fraudStatus === 'accept') {
                $shouldEnroll = true;
            } elseif ($transactionStatus === 'settlement') {
                $shouldEnroll = true;
            }

            // 6. Buat ClassEnrollment jika memenuhi syarat
            if ($shouldEnroll) {
                //  PERBAIKAN UTAMA: gunakan course_class_id dari payment
                $courseClassId = $payment->course_class_id;
                $studentId = $payment->student_id;

                // Cek apakah sudah terdaftar
                $enrollmentExists = ClassEnrollment::where('student_id', $studentId)
                    ->where('class_id', $courseClassId)  // di model ClassEnrollment kolomnya 'class_id'
                    ->exists();

                if (!$enrollmentExists) {
                    ClassEnrollment::create([
                        'student_id' => $studentId,
                        'class_id' => $courseClassId,  // ← disini juga
                        'enrolled_at' => now(),
                        'status' => 'active',
                        'progress_percentage' => 0,
                    ]);

                    Log::info("ClassEnrollment created for student $studentId in class $courseClassId");
                }
                $this->processCommission($payment);
            }

            return response('OK', 200);
        } catch (\Exception $e) {
            Log::error('Midtrans notification error: ' . $e->getMessage() . ' | Order ID: ' . ($orderId ?? 'unknown'));
            return response('OK', 200);
        }
    }

    private function processCommission(Payment $payment)
    {
        // Ambil kursus untuk dapat mentor
        $course = $payment->course;  // pastikan ada relasi 'course'
        $mentorId = $course->created_by;

        // Ambil persentase global
        $setting = Setting::first();
        $percent = $setting->mentor_commission_percent ?? 70;

        // Hitung komisi
        $commissionAmount = ($payment->gross_amount * $percent) / 100;

        // Simpan riwayat
        Commission::create([
            'payment_id' => $payment->id,
            'mentor_id' => $mentorId,
            'amount' => $commissionAmount,
            'percentage' => $percent,
            'paid_at' => now(),  // atau null jika nanti dibayar manual
        ]);
    }

    public function paymentSuccess()
    {
        $classId = session('post_payment_redirect_class_id');

        if ($classId) {
            Session::forget('post_payment_redirect_class_id');
            return redirect()
                ->route('kelas', $classId)
                ->with('success', 'Pembayaran berhasil! Selamat belajar di kelas ini.');
        }

        return redirect()
            ->route('listkursus')
            ->with('info', 'Pembayaran berhasil!');
    }
}
