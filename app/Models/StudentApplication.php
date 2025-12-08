<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudentApplication extends Model
{
    protected $fillable = [
        'application_number',
        'student_name',
        'email',
        'phone',
        'nisn',
        'place_of_birth',
        'birth_date',
        'gender',
        'religion',
        'address',
        'parent_name',
        'parent_phone',
        'parent_address',
        'parent_job',
        'health_info',
        'disability_info',
        'education_history',
        'desired_class',
        'orphan_status',
        'additional_info',
        'documents',
        'medical_info',
        'parent_email',
        'status',
        'notes',
        'application_date',
        'password'
    ];

    protected $casts = [
        'birth_date' => 'date',
        'health_info' => 'array',
        'disability_info' => 'array',
        'education_history' => 'array',
        'additional_info' => 'array',
        'documents' => 'array',
        'orphan_status' => 'string',
        'application_date' => 'date'
    ];
}
