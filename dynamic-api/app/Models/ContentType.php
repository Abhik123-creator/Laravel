<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentType extends Model
{
    protected $fillable = [
        'name', 
        'slug', 
        'description',
        'is_active',
        'api_rate_limit',
        'require_authentication',
        'captcha_enabled',
        'captcha_difficulty',
        'settings'
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'require_authentication' => 'boolean',
        'captcha_enabled' => 'boolean',
        'settings' => 'array',
    ];

    public function fields()
    {
        return $this->hasMany(FieldDefinition::class);
    }

    public function entries()
    {
        return $this->hasMany(ContentEntry::class);
    }
}
