<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentType extends Model
{
    protected $fillable = ['name', 'slug'];
    public function fields()
    {
        return $this->hasMany(FieldDefinition::class);
    }
    public function entries()
    {
        return $this->hasMany(ContentEntry::class);
    }
}
