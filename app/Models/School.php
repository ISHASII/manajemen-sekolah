<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class School extends Model
{
    protected $fillable = [
        'name',
        'description',
        'vision',
        'mission',
        'address',
        'phone',
        'email',
        'website',
        'logo',
        'facilities',
        'programs',
        'social_media'
    ];

    protected $casts = [
        'facilities' => 'array',
        'programs' => 'array',
        'social_media' => 'array'
    ];
}
