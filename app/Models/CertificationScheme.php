<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model: CertificationScheme
 * Tabel: certification_schemes
 * Definisi master data standar skema sertifikasi LSP.
 */
class CertificationScheme extends Model
{
    use HasFactory;

    /**
     * Properti: $fillable
     * Kolom tabel yang diizinkan untuk mass-assignment.
     */
    protected $fillable = [
        'scheme_code',
        'scheme_name',
        'description',
    ];

    /**
     * FUNGSI: participants()
     * UNTUK APA: Mendefinisikan relasi One-to-Many dari tabel certification_schemes ke tabel participants.
     * Satu skema sertifikasi dapat memiliki banyak peserta (asesi).
     */
    public function participants()
    {
        return $this->hasMany(Participant::class, 'scheme_id');
    }

    /**
     * Relasi ke unit tugas / skema ujian asesmen.
     */
    public function tasks()
    {
        return $this->hasMany(AssessmentTask::class, 'scheme_id');
    }
}
