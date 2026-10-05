<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Package extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'title',
        'slug',
        'description',
        'price',
        'original_price',
        'badge',
        'tag',
        'recipients',
        'image',
        'gallery',
        'active',
    ];

    protected $casts = [
        'gallery' => 'array',
        'recipients' => 'array',
        'active' => 'boolean',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function tiers(): HasMany
    {
        return $this->hasMany(PackageTier::class);
    }

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    public function subcategories()
    {
        return $this->belongsToMany(Subcategory::class, 'package_subcategory')->withTimestamps();
    }
}
