<?php

namespace App\Filament\Pages;

use App\Models\Setting;
use Filament\Actions\Action;
use Filament\Forms\Components\Tabs\Tab;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\Grid;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Split;
use Filament\Forms\Components\Tabs;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;
use Illuminate\Support\Facades\Storage;  // Tambahkan ini

class EditSiteSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static string $view = 'filament.pages.edit-site-settings';
    // --- Pengaturan Navigasi & Label ---
    protected static ?string $navigationLabel = 'Identitas Situs';  // Lebih spesifik (Logo, Nama, Kontak)
    protected static ?string $navigationGroup = 'Konten Website';  // Disatukan dengan Slider & FAQ agar tidak terlalu banyak grup
    protected static ?string $slug = 'konfigurasi-situs';
    // --- Pengaturan Visual ---
    protected static ?string $navigationIcon = 'heroicon-o-adjustments-horizontal';  // Ikon pengaturan yang lebih modern
    protected static ?string $activeNavigationIcon = 'heroicon-s-adjustments-horizontal';
    protected static ?int $navigationSort = 3;
    protected ?string $heading = 'Konfigurasi Identitas Situs';
    protected ?string $subheading = 'Kelola informasi publik, logo, dan kontak resmi platform Anda.';
    public ?array $data = [];

    public static function canAccess(): bool
    {
        return auth()->user()->isAdmin();
    }

    public function mount(): void
    {
        $setting = Setting::first();
        if ($setting) {
            $this->form->fill($setting->toArray());
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Tabs::make('Pengaturan Utama')
                    ->tabs([
                        Tab::make('Identitas Situs')
                            ->icon('heroicon-m-globe-alt')
                            ->schema([
                                Split::make([
                                    Section::make([
                                        TextInput::make('site_name')->required()->maxLength(50),
                                        Textarea::make('site_description')->rows(4)->required(),
                                        TextInput::make('copyright_text')->required(),
                                    ])->grow(),
                                    Section::make([
                                        FileUpload::make('logo')
                                            ->label('Logo Website')
                                            ->image()
                                            ->imageEditor()
                                            ->directory('settings')
                                            ->disk('public')
                                            ->columnSpan(1)
                                            // Menghapus file secara otomatis dari storage saat klik ikon 'X' di form
                                            ->reorderable()
                                            ->preserveFilenames(false),  // Memastikan nama unik
                                        TextInput::make('mentor_commission_percent')
                                            ->numeric()
                                            ->suffix('%')
                                            ->required(),
                                    ])->extraAttributes(['class' => 'w-full md:w-80']),
                                ])->from('md'),
                            ]),
                        Tab::make('Hero Section')
                            ->icon('heroicon-m-photo')
                            ->schema([
                                Section::make('Visual Utama (Hero)')
                                    ->schema([
                                        Grid::make(2)
                                            ->schema([
                                                FileUpload::make('hero_image')
                                                    ->label('Gambar Hero')
                                                    ->image()
                                                    ->imageEditor()
                                                    ->directory('settings')
                                                    ->disk('public')
                                                    ->columnSpan(1)
                                                    ->preserveFilenames(false),
                                                Group::make([
                                                    TextInput::make('hero_title')->maxLength(100),
                                                    Textarea::make('hero_subtitle')->rows(3),
                                                ])->columnSpan(1),
                                            ])
                                            ->columnSpan(1)
                                    ]),
                            ]),
                        // --- TAB 2: KONTAK ---
                        Tab::make('Kontak & Media Sosial')
                            ->icon('heroicon-m-chat-bubble-left-right')
                            ->schema([
                                Grid::make(3)->schema([
                                    Section::make('Informasi Kontak')
                                        ->columnSpan(2)
                                        ->columns(2)
                                        ->schema([
                                            TextInput::make('email')->email()->required()->prefixIcon('heroicon-m-envelope'),
                                            TextInput::make('phone')->tel()->required()->prefixIcon('heroicon-m-phone'),
                                            Textarea::make('address')->columnSpanFull()->rows(2),
                                            TextInput::make('gmaps_embed_url')
                                                ->label('URL Google Maps (Iframe)')
                                                ->columnSpanFull(),
                                        ]),
                                    Section::make('Link Sosial Media')
                                        ->columnSpan(1)
                                        ->schema([
                                            TextInput::make('facebook_url')->prefix('https://facebook.com/'),
                                            TextInput::make('instagram_url')->prefix('https://instagram.com/'),
                                            TextInput::make('twitter_url')->prefix('https://x.com/'),
                                            TextInput::make('linkedin_url')->prefix('https://linkedin.com/in/'),
                                        ]),
                                ]),
                            ]),
                        // --- TAB 3: LEGAL (BARU) ---
                        Tab::make('Halaman Hukum')
                            ->icon('heroicon-m-shield-check')
                            ->schema([
                                Section::make('Kebijakan Privasi')
                                    ->description('Isi kebijakan privasi yang akan tampil di halaman /privacy-policy')
                                    ->schema([
                                        RichEditor::make('privacy_policy')
                                            ->label('')
                                            ->toolbarButtons([
                                                'blockquote',
                                                'bold',
                                                'bulletList',
                                                'h2',
                                                'h3',
                                                'italic',
                                                'link',
                                                'orderedList',
                                                'redo',
                                                'undo',
                                            ]),
                                    ]),
                                Section::make('Syarat & Ketentuan')
                                    ->description('Isi aturan main platform yang akan tampil di halaman /terms')
                                    ->schema([
                                        RichEditor::make('terms_conditions')
                                            ->label('')
                                            ->toolbarButtons([
                                                'blockquote',
                                                'bold',
                                                'bulletList',
                                                'h2',
                                                'h3',
                                                'italic',
                                                'link',
                                                'orderedList',
                                                'redo',
                                                'undo',
                                            ]),
                                    ]),
                            ]),
                    ])
                    ->columnSpanFull()
                    ->persistTabInQueryString(),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan Perubahan')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        try {
            $newData = $this->form->getState();
            $setting = Setting::first();

            if ($setting) {
                // List field yang berupa file
                $fileFields = ['logo', 'hero_image'];

                foreach ($fileFields as $field) {
                    // Cek jika ada file lama DAN (file lama berbeda dengan yang baru atau file baru kosong/dihapus)
                    if ($setting->$field && ($setting->$field !== ($newData[$field] ?? null))) {
                        // Hapus file fisik dari folder storage
                        if (Storage::disk('public')->exists($setting->$field)) {
                            Storage::disk('public')->delete($setting->$field);
                        }
                    }
                }
            }

            // Update atau buat data baru
            Setting::updateOrCreate(['id' => 1], $newData);

            Notification::make()
                ->success()
                ->title('Pengaturan berhasil diperbarui')
                ->send();
        } catch (\Exception $e) {
            Notification::make()
                ->danger()
                ->title('Terjadi kesalahan')
                ->body($e->getMessage())
                ->send();
        }
    }
}
