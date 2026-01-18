<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserAdminController extends Controller
{
    public function index(Request $request)
    {
        $query = User::latest();
        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q
                    ->where('name', 'like', "%{$request->search}%")
                    ->orWhere('email', 'like', "%{$request->search}%");
            });
        }
        $users = $query->paginate(10)->withQueryString();
        return view('admin.user.index', compact('users'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'role' => 'required|in:admin,mentor,student',
            'password' => 'required|string|min:8',
        ]);

        $validated['password'] = Hash::make($request->password);
        User::create($validated);

        return back()->with('success', 'User berhasil ditambahkan!');
    }

    public function update(Request $request, User $user)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'email' => ['required', 'email', Rule::unique('users')->ignore($user->id)],
            'role' => 'required|in:admin,mentor,student',
            'password' => 'nullable|string|min:8',
        ]);

        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);
        return back()->with('success', 'User berhasil diperbarui!');
    }

    public function destroy(User $user)
    {
        if (auth()->id() === $user->id)
            return back()->with('error', 'Dilarang hapus diri sendiri!');
        $user->delete();
        return back()->with('success', 'User berhasil dihapus!');
    }

    //
}
