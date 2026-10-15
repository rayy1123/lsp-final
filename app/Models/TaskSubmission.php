<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model: TaskSubmission
 * Tabel: task_submissions
 * Bukti penyelesaian tugas / ujian praktik asesi untuk ditinjau oleh Asesor / Admin.
 */
class TaskSubmission extends Model
{
    use HasFactory;

    protected $fillable = [
        'task_id',
        'participant_id',
        'user_id',
        'submission_content',
        'file_path',
        'file_name',
        'file_type',
        'status',
        'is_revised',
        'revision_count',
        'admin_notes',
    ];

    protected $casts = [
        'is_revised' => 'boolean',
        'revision_count' => 'integer',
    ];

    protected $appends = [
        'file_url',
        'is_image',
    ];

    public function getFileUrlAttribute(): ?string
    {
        return $this->fileUrl();
    }

    public function getIsImageAttribute(): bool
    {
        return $this->isImage();
    }

    /**
     * Cek apakah berkas yang diunggah berupa gambar.
     */
    public function isImage(): bool
    {
        if ($this->file_type === 'image') return true;
        if (!$this->file_path) return false;
        $ext = strtolower(pathinfo($this->file_path, PATHINFO_EXTENSION));
        return in_array($ext, ['jpg', 'jpeg', 'png', 'webp', 'gif', 'svg']);
    }

    /**
     * URL aset publik untuk file yang diunggah.
     */
    public function fileUrl(): ?string
    {
        if (!$this->file_path) return null;
        return asset($this->file_path);
    }

    public function task()
    {
        return $this->belongsTo(AssessmentTask::class, 'task_id');
    }

    public function participant()
    {
        return $this->belongsTo(Participant::class, 'participant_id');
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'user_id');
    }
}
