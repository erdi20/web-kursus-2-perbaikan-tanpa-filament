<?php

namespace App\Http\Livewire;

use Filament\Notifications\Notification;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Livewire\Component;

class DeleteUser extends Component
{
    public $password;

    protected $rules = [
        'password' => 'required|string',
    ];

    public function confirmDelete()
    {
        $this->validate();

        if (!Hash::check($this->password, Auth::user()->password)) {
            Notification::make()
                ->title('Gagal')
                ->body('Kata sandi salah.')
                ->danger()
                ->send();

            return;
        }

        $user = Auth::user();
        Auth::logout();
        $user->delete();

        return redirect('/');  // atau ke halaman login
    }

    public function render()
    {
        return view('livewire.delete-user');
    }
}
