<?php

namespace App\Livewire\MyModernProfile;

use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Forms;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Livewire\Component;

class UpdatePasswordForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill();  // Tidak perlu mengisi data karena ini form update password
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                TextInput::make('current_password')
                    ->label('Kata Sandi Saat Ini')
                    ->password()
                    ->required()
                    ->currentPassword()
                    ->revealable(),
                TextInput::make('password')
                    ->label('Kata Sandi Baru')
                    ->password()
                    ->required()
                    ->confirmed()
                    ->rules(['min:8', 'regex:/[a-z]/', 'regex:/[A-Z]/', 'regex:/[0-9]/', 'regex:/[@$!%*#?&]/'])  // Contoh aturan password kuat
                    ->validationAttribute('kata sandi baru')
                    ->revealable(),
                TextInput::make('password_confirmation')
                    ->label('Konfirmasi Kata Sandi Baru')
                    ->password()
                    ->required()
                    ->revealable(),
            ])
            ->statePath('data');
    }

    public function updatePassword(): void
    {
        try {
            $data = $this->form->getState();
            Auth::user()->update([
                'password' => Hash::make($data['password']),
            ]);
            $this->form->fill();  // Reset form setelah update
            Notification::make()
                ->success()
                ->title('Kata sandi berhasil diperbarui.')
                ->send();
        } catch (ValidationException $e) {
            Notification::make()
                ->danger()
                ->title('Gagal memperbarui kata sandi.')
                ->body($e->getMessage())
                ->send();
            throw $e;  // Re-throw untuk menampilkan error di form
        }
    }

    public function render(): View
    {
        return view('livewire.my-modern-profile.update-password-form');
    }
}
