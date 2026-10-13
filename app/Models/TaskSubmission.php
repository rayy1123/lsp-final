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
        'status',
        'admin_notes',
    ];

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
