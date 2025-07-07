<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FieldDefinition extends Model
{
    protected $fillable = [
        'content_type_id',
        'name',
        'label',
        'type',
    ];

    public function contentType()
    {
        return $this->belongsTo(ContentType::class);
    }
}
