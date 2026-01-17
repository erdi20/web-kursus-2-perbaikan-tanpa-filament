<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Models\Contracts\HasAvatar;
use Filament\Panel;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Storage;

class User extends Authenticatable implements HasAvatar, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'phone',
        'address',
        'birth_date',
        'gender',
        'education_level',
        'role',
        'bio',
        'account_name',
        'account_number',
        'bank_name',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    public function getFilamentAvatarUrl(): ?string
    {
        return $this->avatar_url ? Storage::url($this->avatar_url) : null;
    }

    public function getInitialsAttribute(): string
    {
        // Memecah nama berdasarkan spasi
        $words = explode(' ', $this->name);

        // Mengambil huruf pertama dari kata pertama dan kata terakhir
        $initials = strtoupper(substr($words[0], 0, 1));

        if (count($words) > 1) {
            $initials .= strtoupper(substr(end($words), 0, 1));
        }

        return $initials;
    }

    public function enrollments(): HasMany
    {
        // Model ClassEnrollment menggunakan foreign key 'student_id'
        // untuk merujuk ke 'users.id'.
        return $this->hasMany(ClassEnrollment::class, 'student_id');
    }

    // policy
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    // Method untuk mengecek apakah user adalah Mentor
    public function isMentor(): bool
    {
        return $this->role === 'mentor';
    }

    // relasi
    public function attendances()
    {
        return $this->hasMany(Attendance::class, 'student_id');
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class, 'mentor_id');
    }

    public function canAccessPanel(Panel $panel): bool
    {
        // Student otomatis ditolak (false)
        // Admin dan Mentor diizinkan (true)
        return $this->isAdmin() || $this->isMentor();
    }
}
