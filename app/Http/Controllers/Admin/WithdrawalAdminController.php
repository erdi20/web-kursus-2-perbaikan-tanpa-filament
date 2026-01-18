<?php

namespace App\Http\Controllers\Admin;

use App\Models\Withdrawal;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class WithdrawalAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = Withdrawal::with('mentor')->latest();

        // Filter berdasarkan status jika ada
        if ($request->has('status') && $request->status != '') {
            $query->where('status', $request->status);
        }

        $withdrawals = $query->paginate(10);
        return view('admin.withdrawal.index', compact('withdrawals'));
    }

    public function process(Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'pending') {
            return back()->with('error', 'Hanya permintaan pending yang bisa diproses.');
        }

        $withdrawal->update([
            'status' => 'processed',
            'processed_at' => now(),
        ]);

        return back()->with('success', "Pencairan dana {$withdrawal->mentor->name} sedang diproses.");
    }

    public function complete(Withdrawal $withdrawal)
    {
        if ($withdrawal->status !== 'processed') {
            return back()->with('error', 'Hanya permintaan yang sedang diproses yang bisa diselesaikan.');
        }

        $withdrawal->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);

        return back()->with('success', "Pencairan dana {$withdrawal->mentor->name} telah selesai.");
    }

    //
}
