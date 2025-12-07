<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSkill extends Model
{
    protected $fillable = [
        'student_id',
        'skill_name',
        'skill_category',
        'proficiency_level',
        'description',
        'certificate_file',
        'assessed_date',
        'assessed_by'
    ];

    protected $casts = [
        'assessed_date' => 'date'
    ];

    // Relationships
    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function assessor()
    {
        return $this->belongsTo(User::class, 'assessed_by');
    }
}
