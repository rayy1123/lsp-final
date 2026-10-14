<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable(['name', 'email', 'password', 'role', 'phone_number', 'address'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Cek apakah pengguna memiliki hak akses Administrator.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Cek apakah pengguna adalah Asesi / User biasa.
     */
    public function isUser(): bool
    {
        return $this->role === 'user' || empty($this->role);
    }

    /**
     * Alias method untuk mengecek role asesi.
     */
    public function isAsesi(): bool
    {
        return $this->isUser();
    }

    /**
     * Relasi ke data asesi/peserta berdasarkan email.
     */
    public function participant()
    {
        return $this->hasOne(Participant::class, 'email', 'email');
    }

    /**
     * Relasi ke data submisi tugas ujian asesi.
     */
    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class, 'user_id');
    }

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
}
