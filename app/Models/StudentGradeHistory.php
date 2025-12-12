<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentGradeHistory extends Model
{
    protected $table = 'student_grade_history';

    protected $fillable = [
        'student_id',
        'class_id',
        'class_name',
        'academic_year',
        'semester',
        'average_grade',
        'subjects_grades',
        'status',
        'notes',
        'completed_at'
    ];

    protected $casts = [
        'subjects_grades' => 'array',
        'completed_at' => 'date',
        'average_grade' => 'decimal:2'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }
}
