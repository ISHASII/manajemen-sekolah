<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Subject extends Model
{
    protected $fillable = [
        'name',
        'code',
        'description',
        'credit_hours',
        'category',
        'is_active'
    ];

    protected $casts = [
        'is_active' => 'boolean'
    ];

    // Relationships
    public function schedules()
    {
        return $this->hasMany(Schedule::class);
    }

    public function grades()
    {
        return $this->hasMany(Grade::class);
    }

    public function teachingMaterials()
    {
        return $this->hasMany(TeachingMaterial::class);
    }
}
