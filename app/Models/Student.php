<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Models\TrainingClass;

class Student extends Model
{
    protected $fillable = [
        'user_id',
        'student_id',
        'class_id',
        'nisn',
        'place_of_birth',
        'birth_date',
        'religion',
        'address',
        'parent_name',
        'parent_phone',
        'parent_address',
        'parent_job',
        'parent_email',
        'health_info',
        'disability_info',
        'education_history',
        'interests_talents',
        'job_interest',
        'cv_link',
        'portfolio_links',
        'status',
        'is_orphan',
        'orphan_status',
        'enrollment_date'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'health_info' => 'array',
        'disability_info' => 'array',
        'education_history' => 'array',
        'parent_email' => 'string',
        'medical_info' => 'string',
        'interests_talents' => 'array',
        'portfolio_links' => 'array',
        'is_orphan' => 'boolean',
        'enrollment_date' => 'date'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function skills()
    {
        return $this->hasMany(StudentSkill::class);
    }

    public function trainingClasses()
    {
        return $this->belongsToMany(TrainingClass::class, 'student_training_class')
            ->withTimestamps()
            ->withPivot(['enrolled_at', 'status']);
    }

    public function alumni()
    {
        return $this->hasOne(Alumni::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }

    public function portfolios()
    {
        return $this->hasMany(\App\Models\StudentPortfolio::class);
    }

    public function gradeHistory()
    {
        return $this->hasMany(StudentGradeHistory::class)->orderBy('academic_year', 'desc')->orderBy('semester', 'desc');
    }
}
