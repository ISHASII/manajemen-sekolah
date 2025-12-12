<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TeachingMaterial extends Model
{
    protected $fillable = [
        'teacher_id', 'class_id', 'training_class_id', 'subject_id', 'title', 'description', 'file_path', 'file_type', 'is_visible'
    ];

    protected $casts = [
        'is_visible' => 'boolean'
    ];

    public function teacher()
    {
        return $this->belongsTo(User::class, 'teacher_id');
    }

    public function classRoom()
    {
        return $this->belongsTo(ClassRoom::class, 'class_id');
    }

    public function trainingClass()
    {
        return $this->belongsTo(TrainingClass::class, 'training_class_id');
    }

    public function subject()
    {
        return $this->belongsTo(Subject::class);
    }

    public function submissions()
    {
        return $this->hasMany(\App\Models\StudentSubmission::class, 'material_id');
    }
}
