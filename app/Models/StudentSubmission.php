<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentSubmission extends Model
{
    protected $fillable = [
        'student_id', 'material_id', 'file_path', 'file_type', 'link', 'description'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }

    public function material()
    {
        return $this->belongsTo(TeachingMaterial::class, 'material_id');
    }
}
