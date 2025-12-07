<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Document extends Model
{
    protected $fillable = [
        'documentable_type',
        'documentable_id',
        'document_type',
        'document_name',
        'file_path',
        'file_size',
        'mime_type',
        'is_verified',
        'verified_by',
        'verified_at',
        'notes'
    ];

    protected $casts = [
        'is_verified' => 'boolean',
        'verified_at' => 'datetime'
    ];

    // Polymorphic relationship
    public function documentable()
    {
        return $this->morphTo();
    }

    public function verifier()
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}
