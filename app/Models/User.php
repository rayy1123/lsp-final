<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Fillable([
    'name',
    'nik',
    'gender',
    'education_type',
    'academic_title',
    'email',
    'password',
    'role',
    'phone_number',
    'address',
    'id_card_file',
    'report_sheet_file',
    'certificate_file',
    'diploma_file',
    'photo_file',
    'portfolio_file',
])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * URL berkas KTP / Kartu Pelajar.
     */
    public function idCardUrl(): ?string
    {
        return $this->id_card_file ? asset($this->id_card_file) : null;
    }

    /**
     * URL berkas Rapor Terakhir.
     */
    public function reportSheetUrl(): ?string
    {
        return $this->report_sheet_file ? asset($this->report_sheet_file) : null;
    }

    /**
     * URL berkas Sertifikat Terkait.
     */
    public function certificateUrl(): ?string
    {
        return $this->certificate_file ? asset($this->certificate_file) : null;
    }

    /**
     * URL berkas Ijazah Terakhir / Surat Keterangan.
     */
    public function diplomaUrl(): ?string
    {
        return $this->diploma_file ? asset($this->diploma_file) : null;
    }

    /**
     * URL pas foto formal.
     */
    public function photoUrl(): ?string
    {
        return $this->photo_file ? asset($this->photo_file) : null;
    }

    /**
     * URL berkas portofolio / dokumen pendukung.
     */
    public function portfolioUrl(): ?string
    {
        return $this->portfolio_file ? asset($this->portfolio_file) : null;
    }

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
