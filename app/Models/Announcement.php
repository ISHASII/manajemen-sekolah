<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Announcement extends Model
{
    protected $fillable = [
        'title',
        'content',
        'type',
        'target_audience',
        'created_by',
        'is_active',
        'publish_date',
        'expire_date',
        'image'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'publish_date' => 'date',
        'expire_date' => 'date'
    ];

    // Relationships
    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
