<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Alumni extends Model
{
    /**
     * Explicit table name: default pluralization for "Alumni" may result
     * in an incorrect table name like "alumnis". Make sure we use the
     * migration-created table name: "alumni".
     */
    protected $table = 'alumni';
    protected $fillable = [
        'student_id',
        'graduation_date',
        'graduation_year',
        'graduation_class',
        'final_grade',
        'skills',
        'portfolio',
        'work_interests',
        'current_job',
        'current_company',
        'cv_online',
        'linkedin_profile',
        'achievements',
        'training_history',
        'notes'
    ];

    protected $casts = [
        'graduation_date' => 'date',
        'skills' => 'array',
        'portfolio' => 'array',
        'work_interests' => 'array',
        'achievements' => 'array',
        'training_history' => 'array',
        'final_grade' => 'decimal:2'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
