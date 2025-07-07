<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentEntry extends Model
{
    protected $fillable = [
        'content_type_id',
        'data',
    ];

    protected $casts = [
        'data' => 'array',
    ];

    public function contentType()
    {
        return $this->belongsTo(ContentType::class);
    }
}
