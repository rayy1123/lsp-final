<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model: AssessmentTask
 * Tabel: assessment_tasks
 * Definisi master data tugas / unit ujian praktik asesmen untuk skema sertifikasi.
 */
class AssessmentTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'scheme_id',
        'task_code',
        'title',
        'description',
        'due_date',
    ];

    protected $casts = [
        'due_date' => 'datetime',
    ];

    public function scheme()
    {
        return $this->belongsTo(CertificationScheme::class, 'scheme_id');
    }

    public function submissions()
    {
        return $this->hasMany(TaskSubmission::class, 'task_id');
    }
}
