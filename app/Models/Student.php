<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

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
        'health_info',
        'disability_info',
        'education_history',
        'interests_talents',
        'status',
        'is_orphan',
        'enrollment_date'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'health_info' => 'array',
        'disability_info' => 'array',
        'education_history' => 'array',
        'interests_talents' => 'array',
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

    public function alumni()
    {
        return $this->hasOne(Alumni::class);
    }

    public function documents()
    {
        return $this->morphMany(Document::class, 'documentable');
    }
}
