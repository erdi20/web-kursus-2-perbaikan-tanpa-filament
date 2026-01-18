<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Faq;
use Illuminate\Http\Request;

class FaqAdminController extends Controller
{
    public function index()
    {
        $faqs = Faq::orderBy('order', 'asc')->get();
        return view('admin.faq.index', compact('faqs'));
    }

    public function create()
    {
        $nextOrder = Faq::max('order') + 1;
        return view('admin.faq.editcreate', compact('nextOrder'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'required|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Faq::create($validated);
        return redirect()->route('admin.faq')->with('success', 'FAQ berhasil dibuat!');
    }

    public function edit(Faq $faq)
    {
        return view('admin.faq.editcreate', compact('faq'));
    }

    public function update(Request $request, Faq $faq)
    {
        $validated = $request->validate([
            'question' => 'required|string|max:255',
            'answer' => 'required|string',
            'order' => 'required|integer',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $faq->update($validated);
        return redirect()->route('admin.faq')->with('success', 'FAQ diperbarui!');
    }

    public function destroy(Faq $faq)
    {
        $faq->delete();
        return redirect()->route('admin.faq')->with('success', 'FAQ dihapus!');
    }

    public function toggle(Faq $faq)
    {
        $faq->update(['is_active' => !$faq->is_active]);
        return back()->with('success', 'Status FAQ berhasil diubah!');
    }

    //
}
