<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model: Participant
 * Tabel: participants
 * Definisi entitas data peserta uji sertifikasi (asesi).
 */
class Participant extends Model
{
    use HasFactory;

    /**
     * Properti: $fillable
     * Kolom tabel yang diizinkan untuk mass-assignment.
     */
    protected $fillable = [
        'scheme_id',
        'registration_number',
        'full_name',
        'email',
        'phone_number',
        'address',
        'status',
    ];

    /**
     * FUNGSI: scheme()
     * UNTUK APA: Mendefinisikan relasi Many-to-One (BelongsTo) dari tabel participants ke certification_schemes.
     * Mengambil detail skema sertifikasi yang diikuti oleh peserta bersangkutan.
     */
    public function scheme()
    {
        return $this->belongsTo(CertificationScheme::class, 'scheme_id');
    }
}
