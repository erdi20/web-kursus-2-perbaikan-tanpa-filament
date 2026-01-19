<?php

namespace App\Http\Controllers\Mentor;

use App\Http\Controllers\Controller;
use App\Models\Course;
use App\Models\Payment;
use App\Models\Withdrawal;
use Illuminate\Http\Request;

class KeuanganMentorController extends Controller
{
    // public function index()
    // {
    //     $user = auth()->user();
    //     $mentorId = $user->id;
    //     $courseIds = \App\Models\Course::where('created_by', $mentorId)->pluck('id');

    //     // Hitung Saldo sesuai logika Filament lo
    //     $totalRevenue = \App\Models\Payment::whereIn('course_id', $courseIds)
    //         ->whereIn('transaction_status', ['settlement', 'capture'])
    //         ->sum('gross_amount');

    //     $totalWithdrawn = \App\Models\Withdrawal::where('mentor_id', $mentorId)
    //         ->where('status', 'completed')
    //         ->sum('amount');

    //     $availableBalance = $totalRevenue - $totalWithdrawn;

    //     $transactions = \App\Models\Payment::with(['course', 'user'])
    //         ->whereIn('course_id', $courseIds)
    //         ->whereIn('transaction_status', ['settlement', 'capture'])
    //         ->latest('settlement_at')
    //         ->paginate(10, ['*'], 't_page');

    //     $withdrawals = \App\Models\Withdrawal::where('mentor_id', $mentorId)
    //         ->latest()
    //         ->paginate(10, ['*'], 'w_page');

    //     return view('mentor.keuangan.index', compact(
    //         'totalRevenue', 'totalWithdrawn', 'availableBalance',
    //         'transactions', 'withdrawals', 'user'
    //     ));
    // }

    // ------

    public function index()
    {
        $user = auth()->user();
        $mentorId = $user->id;

        // Ambil hanya komisi dari pembayaran SUKSES
        $commissionQuery = \App\Models\Commission::with('payment.course')
            ->where('mentor_id', $mentorId)
            ->whereHas('payment', fn($q) => $q->whereIn('transaction_status', ['settlement', 'capture']));

        $totalRevenue = $commissionQuery->clone()->sum('amount');

        $totalWithdrawn = \App\Models\Withdrawal::where('mentor_id', $mentorId)
            ->where('status', 'completed')
            ->sum('amount');

        $availableBalance = $totalRevenue - $totalWithdrawn;

        $transactions = $commissionQuery->latest()->paginate(10, ['*'], 't_page');

        $withdrawals = \App\Models\Withdrawal::where('mentor_id', $mentorId)
            ->latest()
            ->paginate(10, ['*'], 'w_page');

        return view('mentor.keuangan.index', compact(
            'totalRevenue',
            'totalWithdrawn',
            'availableBalance',
            'transactions',
            'withdrawals',
            'user'
        ));
    }

    public function storeWithdrawal(Request $request)
    {
        $user = auth()->user();

        // Logic hitung saldo (sudah benar)
        $courseIds = \App\Models\Course::where('created_by', $user->id)->pluck('id');
        $totalRevenue = \App\Models\Payment::whereIn('course_id', $courseIds)
            ->whereIn('transaction_status', ['settlement', 'capture'])
            ->sum('gross_amount');
        $totalWithdrawn = \App\Models\Withdrawal::where('mentor_id', $user->id)
            ->where('status', 'completed')
            ->sum('amount');
        $available = $totalRevenue - $totalWithdrawn;

        // Validasi
        $request->validate([
            'amount' => "required|numeric|min:50000|max:$available",
        ], [
            'amount.max' => 'Saldo lo nggak cukup, Bro!',
            'amount.min' => 'Minimal pencairan itu Rp 50.000',
        ]);

        try {
            // Simpan ke database
            \App\Models\Withdrawal::create([
                'mentor_id' => $user->id,
                'amount' => $request->amount,
                'account_name' => $user->account_name,
                'account_number' => $user->account_number,
                'bank_name' => $user->bank_name,
                'status' => 'pending',
            ]);

            return back()->with('success', 'Permintaan pencairan berhasil dikirim ke Admin!');
        } catch (\Exception $e) {
            // Balikin error ke tampilan kalau gagal simpan
            return back()->withErrors(['error' => 'Gagal simpan: ' . $e->getMessage()]);
        }
    }
}
