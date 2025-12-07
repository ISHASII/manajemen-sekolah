<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Teacher extends Model
{
    protected $fillable = [
        'user_id',
        'teacher_id',
        'nip',
        'subjects',
        'qualifications',
        'certifications',
        'hire_date',
        'status'
    ];

    protected $casts = [
        'subjects' => 'array',
        'qualifications' => 'array',
        'certifications' => 'array',
        'hire_date' => 'date'
    ];

    // Relationships
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function homeroomClasses()
    {
        return $this->hasMany(ClassRoom::class, 'homeroom_teacher_id', 'user_id');
    }

    public function schedules()
    {
        return $this->hasMany(Schedule::class, 'teacher_id', 'user_id');
    }

    public function grades()
    {
        return $this->hasMany(Grade::class, 'teacher_id', 'user_id');
    }
}
