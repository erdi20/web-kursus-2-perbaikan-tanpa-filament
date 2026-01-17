<?php

namespace App\Filament\Pages;

use Filament\Actions\Concerns\InteractsWithActions;
use Filament\Actions\Contracts\HasActions;
use Filament\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class MyModernProfile extends Page implements HasActions
{
    use InteractsWithActions;

    protected static ?string $navigationIcon = 'heroicon-o-user';
    protected static ?string $title = 'Profil Pengguna Saya';
    protected static string $view = 'filament.pages.my-modern-profile';
    protected static bool $shouldRegisterNavigation = false;

    /**
     * Daftarkan Action di sini agar mountAction() bisa menemukannya
     */
    protected function getActions(): array
    {
        return [
            $this->deleteAccountAction(),
        ];
    }

    /**
     * Definisi Action Hapus Akun
     */
    // public function deleteAccountAction(): Action
    // {
    //     return Action::make('deleteAccount')
    //         ->label('Hapus Akun')
    //         ->color('danger')
    //         ->requiresConfirmation()
    //         ->modalHeading('Konfirmasi Hapus Akun')
    //         ->modalDescription('Apakah Anda yakin? Semua data Anda akan dihapus permanen. Anda akan langsung dikeluarkan dari sistem.')
    //         ->modalSubmitActionLabel('Ya, Hapus Sekarang')
    //         ->modalIcon('heroicon-o-trash')
    //         ->action(function () {
    //             $user = Auth::user();
    //             // Proses Logout
    //             Auth::logout();
    //             request()->session()->invalidate();
    //             request()->session()->regenerateToken();
    //             // Proses Hapus
    //             $user->delete();
    //             Notification::make()
    //                 ->title('Akun Berhasil Dihapus')
    //                 ->success()
    //                 ->send();
    //             return redirect()->to(filament()->getLoginUrl());
    //         });
    // }
    public function deleteAccountAction(): Action
    {
        return Action::make('deleteAccount')
            ->label('Hapus Akun')
            ->color('danger')
            ->requiresConfirmation()
            ->modalHeading('Konfirmasi Hapus Akun')
            ->modalDescription('Apakah Anda yakin? Semua data Anda akan dihapus permanen.')
            ->modalSubmitActionLabel('Ya, Hapus Sekarang')
            ->action(function () {
                $user = Auth::user();

                // 1. Proses Logout
                Auth::logout();

                // 2. Gunakan Facade Session untuk menghindari error 'Expected object'
                Session::invalidate();
                Session::regenerateToken();

                // 3. Proses Hapus User
                if ($user) {
                    $user->delete();
                }

                Notification::make()
                    ->title('Akun Berhasil Dihapus')
                    ->success()
                    ->send();

                return redirect()->to(filament()->getLoginUrl());
            });

    }

}
