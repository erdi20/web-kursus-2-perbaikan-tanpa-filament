<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProfileUpdateRequest;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class ProfileController extends Controller
{
    /**
     * Display the user's profile form.
     */
    public function edit(Request $request): View
    {
        return view('profile.edit', [
            'user' => $request->user(),
        ]);
    }

    /**
     * Update the user's profile information.
     */
    public function update(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        $user->fill($request->validated());

        $request->validate([
            'avatar' => ['nullable', 'image', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', Rule::in(['male', 'female', 'other'])],
            'education_level' => ['nullable', 'string', 'max:255'],
        ]);

        if ($request->hasFile('avatar')) {
            $file = $request->file('avatar');

            if ($user->avatar_url) {
                Storage::disk('public')->delete($user->avatar_url);
            }

            $path = $file->storeAs(
                'avatars',
                $file->hashName(),
                'public'
            );

            $user->avatar_url = $path;
        } elseif ($request->boolean('remove_avatar') && $user->avatar_url) {
            Storage::disk('public')->delete($user->avatar_url);
            $user->avatar_url = null;
        }

        $user->phone = $request->phone;
        $user->address = $request->address;
        $user->birth_date = $request->birth_date;
        $user->gender = $request->gender;
        $user->education_level = $request->education_level;

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return Redirect::route('profile.edit')->with('status', 'profile-updated');
    }

    /**
     * Delete the user's account.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $request->validateWithBag('userDeletion', [
            'password' => ['required', 'current_password'],
        ]);

        $user = $request->user();

        Auth::logout();

        $user->delete();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return Redirect::to('/');
    }

    // ProfileController.php

    public function destroyUserFilament(Request $request)
    {
        $request->validate(['password' => 'required']);

        if (!Hash::check($request->password, Auth::user()->password)) {
            return back()->withErrors(['password' => 'Kata sandi salah.']);
        }

        Auth::user()->delete();
        Auth::logout();

        return redirect('/');
    }

    public function editMentor(Request $request)
    {
        return view('profile.mentor-edit', [
            'user' => $request->user(),
        ]);
    }

    public function updateMentor(ProfileUpdateRequest $request): RedirectResponse
    {
        $user = $request->user();

        // 1. Validasi Dasar + Field Tambahan (Termasuk Rekening)
        $rules = [
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'lowercase', 'email', 'max:255', 'unique:users,email,' . $user->id],
            'avatar' => ['nullable', 'image', 'max:2048'],
            'remove_avatar' => ['nullable', 'boolean'],
            'phone' => ['nullable', 'string', 'max:20'],
            'address' => ['nullable', 'string'],
            'birth_date' => ['nullable', 'date'],
            'gender' => ['nullable', Rule::in(['male', 'female'])],
            'education_level' => ['nullable', 'string', 'max:255'],
            'bio' => ['nullable', 'string', 'max:1000'],
        ];

        // 2. Tambahkan Validasi REKENING khusus jika USER adalah MENTOR
        if ($user->isMentor()) {
            $rules['bank_name'] = ['required', 'string', 'max:100'];
            $rules['account_number'] = ['required', 'numeric'];
            $rules['account_name'] = ['required', 'string', 'max:100'];
        }

        $validated = $request->validate($rules);

        // 3. Handle Avatar (Sama seperti logic lo sebelumnya)
        if ($request->hasFile('avatar')) {
            if ($user->avatar_url) {
                Storage::disk('public')->delete($user->avatar_url);
            }
            $user->avatar_url = $request->file('avatar')->store('avatars', 'public');
        } elseif ($request->boolean('remove_avatar') && $user->avatar_url) {
            Storage::disk('public')->delete($user->avatar_url);
            $user->avatar_url = null;
        }

        // 4. Isi data ke Model (Mass Assignment)
        $user->fill([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'],
            'address' => $validated['address'],
            'birth_date' => $validated['birth_date'],
            'gender' => $validated['gender'],
            'education_level' => $validated['education_level'],
            'bio' => $validated['bio'],
        ]);

        // 5. Simpan Data Rekening Jika Mentor
        if ($user->isMentor()) {
            $user->bank_name = $validated['bank_name'];
            $user->account_number = $validated['account_number'];
            $user->account_name = $validated['account_name'];
        }

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        // 6. Redirect dinamis berdasarkan role (agar navbar/layout tidak bentrok)
        $route = $user->isMentor() ? 'mentor.mentoredit' : 'mentoredit';

        return Redirect::route($route)->with('status', 'profile-updated');
    }
}
