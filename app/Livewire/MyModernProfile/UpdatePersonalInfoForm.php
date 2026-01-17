<?php

namespace App\Livewire\MyModernProfile;

use Filament\Forms\Components\DatePicker;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Forms;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class UpdatePersonalInfoForm extends Component implements HasForms
{
    use InteractsWithForms;

    public ?array $data = [];

    public function mount(): void
    {
        $user = auth()->user();

        // 1. Masukkan semua kolom ke dalam form fill
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'avatar_url' => $user->avatar_url,
            'phone' => $user->phone,
            'address' => $user->address,
            'birth_date' => $user->birth_date,
            'gender' => $user->gender,
            'education_level' => $user->education_level,
            'bio' => $user->bio,
            'account_name' => $user->isMentor() ? $user->account_name : null,
            'account_number' => $user->isMentor() ? $user->account_number : null,
            'bank_name' => $user->isMentor() ? $user->bank_name : null,
        ]);
    }

    // public function form(Form $form): Form
    // {
    //     $isMentor = auth()->user()->isMentor();

    //     return $form
    //         ->schema([
    //             // Bagian Avatar
    //             FileUpload::make('avatar_url')
    //                 ->label('Foto Profil')
    //                 ->disk('public')
    //                 ->directory('avatars')
    //                 ->image()
    //                 ->avatar()
    //                 ->columnSpanFull(),
    //             // Grid untuk Nama dan Email
    //             Grid::make(2)
    //                 ->schema([
    //                     TextInput::make('name')
    //                         ->label('Nama Lengkap')
    //                         ->required()
    //                         ->maxLength(255),
    //                     TextInput::make('email')
    //                         ->label('Alamat Email')
    //                         ->email()
    //                         ->required()
    //                         ->unique(table: 'users', ignorable: auth()->user()),
    //                 ]),
    //             // Input untuk kolom umum
    //             Grid::make(2)
    //                 ->schema([
    //                     TextInput::make('phone')
    //                         ->label('Nomor Telepon')
    //                         ->tel()
    //                         ->placeholder('0812xxxx'),
    //                     DatePicker::make('birth_date')
    //                         ->label('Tanggal Lahir')
    //                         ->native(false)
    //                         ->displayFormat('d/m/Y'),
    //                     Select::make('gender')
    //                         ->label('Jenis Kelamin')
    //                         ->options([
    //                             'male' => 'Laki-laki',
    //                             'female' => 'Perempuan',
    //                         ]),
    //                     Select::make('education_level')
    //                         ->label('Tingkat Pendidikan')
    //                         ->options([
    //                             'SD' => 'SD',
    //                             'SMP' => 'SMP',
    //                             'SMA/SMK' => 'SMA/SMK',
    //                             'S1' => 'S1',
    //                             'S2' => 'S2',
    //                             'S3' => 'S3',
    //                         ]),
    //                 ]),
    //             // Bagian khusus MENTOR (Menggunakan visible agar tidak error)
    //             Grid::make(2)
    //                 ->schema([
    //                     TextInput::make('account_name')
    //                         ->label('Nama Pemilik Rekening')
    //                         ->required()
    //                         ->maxLength(100),
    //                     TextInput::make('bank_name')
    //                         ->label('Nama Bank')
    //                         ->required()
    //                         ->placeholder('BCA, BNI, Mandiri, dll.')
    //                         ->maxLength(50),
    //                     TextInput::make('account_number')
    //                         ->label('Nomor Rekening')
    //                         ->required()
    //                         ->numeric()
    //                         ->maxLength(20)
    //                         ->columnSpanFull(),  // Agar terlihat rapi
    //                 ])
    //                 ->visible($isMentor),  // Komponen Grid ini hanya akan muncul jika isMentor true
    //             Textarea::make('address')
    //                 ->label('Alamat Lengkap')
    //                 ->rows(3),
    //             Textarea::make('bio')
    //                 ->label('Bio / Tentang Saya')
    //                 ->rows(3)
    //                 ->placeholder('Ceritakan singkat tentang diri Anda...'),
    //         ])
    //         ->statePath('data');
    // }

    public function form(Form $form): Form
    {
        $isMentor = auth()->user()->isMentor();

        return $form
            ->schema([
                Forms\Components\Section::make('Identitas Visual')
                    ->description('Foto profil membantu orang lain mengenali Anda.')
                    ->schema([
                        FileUpload::make('avatar_url')
                            ->label('Foto Profil')
                            ->disk('public')
                            ->directory('avatars')
                            ->image()
                            ->avatar()
                            ->imageEditor()  // Tambahan agar bisa crop
                            ->alignCenter()
                            ->columnSpanFull(),
                    ])
                    ->collapsible(),
                Forms\Components\Section::make('Informasi Dasar')
                    ->description('Data utama akun Anda.')
                    ->schema([
                        Grid::make(2)->schema([
                            TextInput::make('name')
                                ->label('Nama Lengkap')
                                ->required()
                                ->maxLength(255)
                                ->prefixIcon('heroicon-m-user'),
                            TextInput::make('email')
                                ->label('Alamat Email')
                                ->email()
                                ->required()
                                ->prefixIcon('heroicon-m-envelope')
                                ->unique(table: 'users', ignorable: auth()->user()),
                            TextInput::make('phone')
                                ->label('Nomor Telepon')
                                ->tel()
                                ->prefixIcon('heroicon-m-phone')
                                ->placeholder('0812xxxx'),
                            DatePicker::make('birth_date')
                                ->label('Tanggal Lahir')
                                ->native(false)
                                ->prefixIcon('heroicon-m-calendar')
                                ->displayFormat('d/m/Y'),
                        ]),
                    ]),
                Forms\Components\Section::make('Latar Belakang')
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('gender')
                                ->label('Jenis Kelamin')
                                ->prefixIcon('heroicon-m-users')
                                ->options([
                                    'male' => 'Laki-laki',
                                    'female' => 'Perempuan',
                                ]),
                            Select::make('education_level')
                                ->label('Tingkat Pendidikan')
                                ->prefixIcon('heroicon-m-academic-cap')
                                ->options([
                                    'SD' => 'SD',
                                    'SMP' => 'SMP',
                                    'SMA/SMK' => 'SMA/SMK',
                                    'S1' => 'S1',
                                    'S2' => 'S2',
                                    'S3' => 'S3',
                                ]),
                        ]),
                        Textarea::make('bio')
                            ->label('Bio / Tentang Saya')
                            ->rows(3)
                            ->placeholder('Ceritakan singkat tentang diri Anda...'),
                        Textarea::make('address')
                            ->label('Alamat Lengkap')
                            ->rows(3),
                    ]),
                // Bagian khusus MENTOR
                Forms\Components\Section::make('Informasi Pembayaran')
                    ->description('Digunakan untuk pencairan komisi pengajaran.')
                    ->icon('heroicon-o-banknotes')
                    ->visible($isMentor)
                    ->schema([
                        Grid::make(2)->schema([
                            Select::make('bank_name')
                                ->label('Nama Bank / E-Wallet')
                                ->required()
                                ->searchable()
                                ->preload()
                                ->options([
                                    'Bank Umum' => [
                                        'BCA' => 'Bank Central Asia (BCA)',
                                        'BNI' => 'Bank Negara Indonesia (BNI)',
                                        'BRI' => 'Bank Rakyat Indonesia (BRI)',
                                        'Mandiri' => 'Bank Mandiri',
                                        'BSI' => 'Bank Syariah Indonesia (BSI)',
                                        'CIMB' => 'CIMB Niaga',
                                        'BTPN' => 'BTPN / Jenius',
                                        'Permata' => 'Bank Permata',
                                        'Danamon' => 'Bank Danamon',
                                    ],
                                    'Bank Digital' => [
                                        'Seabank' => 'Seabank',
                                        'Bank_Neo' => 'Bank Neo Commerce',
                                        'Bank_Aladin' => 'Bank Aladin Syariah',
                                        'Jago' => 'Bank Jago',
                                    ],
                                    'E-Wallet' => [
                                        'GoPay' => 'GoPay',
                                        'OVO' => 'OVO',
                                        'Dana' => 'DANA',
                                        'ShopeePay' => 'ShopeePay',
                                        'LinkAja' => 'LinkAja',
                                    ],
                                ]),
                            TextInput::make('account_number')
                                ->label('Nomor Rekening / HP')
                                ->required()
                                ->numeric()
                                ->prefixIcon('heroicon-m-credit-card'),
                            TextInput::make('account_name')
                                ->label('Nama Pemilik Rekening')
                                ->required()
                                ->maxLength(100)
                                ->columnSpanFull()
                                ->helperText('Pastikan nama sesuai dengan yang tertera di buku tabungan atau aplikasi.'),
                        ]),
                    ]),
            ])
            ->statePath('data');
    }

    public function updateProfile(): void
    {
        $data = $this->form->getState();
        $user = \App\Models\User::find(auth()->id());

        // Logika hapus avatar lama (tetap dipertahankan dari kode Anda)
        $newAvatar = $data['avatar_url'] ?? null;
        $oldAvatar = $user->getOriginal('avatar_url');

        if ($oldAvatar && $oldAvatar !== $newAvatar) {
            if (Storage::disk('public')->exists($oldAvatar)) {
                Storage::disk('public')->delete($oldAvatar);
            }
        }

        // 3. Simpan SEMUA data ke database
        // Karena key di $data sudah sama dengan kolom di database, kita bisa gunakan array merge atau update langsung
        $user->update([
            'name' => $data['name'],
            'email' => $data['email'],
            'avatar_url' => $newAvatar,
            'phone' => $data['phone'],
            'address' => $data['address'],
            'birth_date' => $data['birth_date'],
            'gender' => $data['gender'],
            'education_level' => $data['education_level'],
            'bio' => $data['bio'],
        ]);

        if ($user->isMentor()) {
            $updateData['account_name'] = $data['account_name'];
            $updateData['account_number'] = $data['account_number'];
            $updateData['bank_name'] = $data['bank_name'];
        }

        $user->update($updateData);

        $this->dispatch('filament:refresh-user-avatar');

        Notification::make()
            ->success()
            ->title('Profil berhasil diperbarui.')
            ->send();

        // Gunakan reload jika ingin merefresh UI secara total (opsional)
        // $this->js('window.location.reload();');
    }

    public function render(): View
    {
        return view('livewire.my-modern-profile.update-personal-info-form');
    }
}
