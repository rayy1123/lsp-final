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
        'nik',
        'gender',
        'education_type',
        'academic_title',
        'full_name',
        'email',
        'phone_number',
        'address',
        'id_card_file',
        'report_sheet_file',
        'certificate_file',
        'diploma_file',
        'photo_file',
        'portfolio_file',
        'status',
    ];

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
     * Normalisasi status hasil uji kompetensi (BNSP Standard: Pending / Kompeten / Belum Kompeten).
     */
    public function getStatusAttribute($value): string
    {
        if ($value && str_contains($value, 'Kompeten') && !str_contains($value, 'Belum')) {
            return 'Kompeten';
        }
        if ($value && str_contains($value, 'Belum')) {
            return 'Belum Kompeten';
        }
        if ($value && str_contains($value, 'Pending')) {
            return 'Pending';
        }
        return $value ?: 'Pending';
    }

    /**
     * Relasi ke akun user berdasarkan email.
     */
    public function user()
    {
        return $this->belongsTo(User::class, 'email', 'email');
    }

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
