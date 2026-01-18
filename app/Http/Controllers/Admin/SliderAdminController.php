<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Slider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SliderAdminController extends Controller
{
    public function index()
    {
        $sliders = Slider::orderBy('order', 'asc')->get();
        return view('admin.slider.index', compact('sliders'));
    }

    public function create()
    {
        // Ambil order tertinggi lalu tambah 1. Kalau masih kosong, mulai dari 1.
        $nextOrder = Slider::max('order') + 1;
        return view('admin.slider.editcreate', compact('nextOrder'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'required|image|mimes:jpeg,png,jpg|max:2048',
            'order' => 'required|integer',
        ]);

        if ($request->hasFile('image')) {
            $validated['image'] = $request->file('image')->store('sliders', 'public');
        }

        $validated['is_active'] = $request->has('is_active') ? true : false;

        try {
            Slider::create($validated);
            return redirect()->route('admin.slider')->with('success', 'Berhasil');
        } catch (\Exception $e) {
            return back()->withErrors(['error' => 'Gagal menyimpan slider: ' . $e->getMessage()])->withInput();
        }
    }

    public function edit(Slider $slider)
    {
        return view('admin.slider.editcreate', compact('slider'));
    }

    public function update(Request $request, Slider $slider)
    {
        $validated = $request->validate([
            'title' => 'nullable|string|max:255',
            'description' => 'nullable|string',
            'image' => 'nullable|image|mimes:jpeg,png,jpg|max:2048',
            'order' => 'required|integer',
        ]);

        if ($request->hasFile('image')) {
            // Hapus foto lama
            if ($slider->image) {
                Storage::disk('public')->delete($slider->image);
            }
            $validated['image'] = $request->file('image')->store('sliders', 'public');
        }

        $validated['is_active'] = $request->has('is_active');

        $slider->update($validated);
        return redirect()->route('admin.slider')->with('success', 'Slider diperbarui!');
    }

    public function destroy(Slider $slider)
    {
        if ($slider->image) {
            Storage::disk('public')->delete($slider->image);
        }
        $slider->delete();
        return back()->with('success', 'Slider dihapus!');
    }

    public function toggleStatus(Slider $slider)
    {
        $slider->update(['is_active' => !$slider->is_active]);
        return back();
    }
}
