<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Category extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'slug',
        'count',
        'image',
        'active',
        'display_order',
    ];

    protected $casts = [
        'active' => 'boolean',
        'count' => 'integer',
        'display_order' => 'integer',
    ];

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }
}
