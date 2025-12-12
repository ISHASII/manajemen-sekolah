<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class StudentPortfolio extends Model
{
    use HasFactory;

    protected $fillable = [
        'student_id',
        'title',
        'description',
        'link',
        'file_path'
    ];

    public function student()
    {
        return $this->belongsTo(Student::class);
    }
}
