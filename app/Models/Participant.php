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

    /**
     * Relasi ke bukti penyelesaian tugas / ujian asesi.
     */
    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class, 'participant_id');
    }

    /**
     * Total unit tugas yang ada pada skema sertifikasi peserta.
     */
    public function totalTasksCount(): int
    {
        return $this->scheme ? $this->scheme->tasks()->count() : 0;
    }

    /**
     * Jumlah unit tugas yang telah berstatus 'Diterima' oleh asesor.
     */
    public function acceptedTasksCount(): int
    {
        return $this->submissions()->where('status', 'Diterima')->count();
    }

    /**
     * Persentase penyelesaian tugas ujian peserta (0 - 100%).
     */
    public function taskProgressPercentage(): int
    {
        $total = $this->totalTasksCount();
        if ($total === 0) return 0;
        return (int) round(($this->acceptedTasksCount() / $total) * 100);
    }

    /**
     * Apakah peserta sudah memenuhi seluruh tugas ujian (100% diterima)?
     */
    public function isEligibleForCompetence(): bool
    {
        $total = $this->totalTasksCount();
        if ($total === 0) return false;
        return $this->acceptedTasksCount() >= $total;
    }
}
