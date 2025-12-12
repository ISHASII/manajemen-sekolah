<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\User;
use App\Models\Student;
use App\Models\Teacher;

class TrainingClass extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'start_at',
        'end_at',
        'capacity',
        'trainer_id',
        'is_active',
        'created_by',
        'open_to_kejuruan'
    ];

    protected $casts = [
        'start_at' => 'datetime',
        'end_at' => 'datetime',
        'is_active' => 'boolean',
        'open_to_kejuruan' => 'boolean'
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function trainer()
    {
        return $this->belongsTo(Teacher::class, 'trainer_id');
    }

    public function students()
    {
        return $this->belongsToMany(Student::class, 'student_training_class')
            ->withTimestamps()
            ->withPivot(['enrolled_at', 'status']);
    }
}
