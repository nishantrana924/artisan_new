<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PackageTier extends Model
{
    use HasFactory;

    protected $fillable = [
        'package_id',
        'name',
        'slug',
        'price',
        'description',
        'image',
        'inclusions',
        'active',
        'display_order',
    ];

    protected $casts = [
        'inclusions' => 'array',
        'price' => 'decimal:2',
        'active' => 'boolean',
    ];

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }

    /**
     * Automatically clear catalog cache whenever a package tier is saved or deleted.
     */
    protected static function booted(): void
    {
        static::saved(fn() => \App\Services\CelebrationCatalogService::clearCache());
        static::deleted(fn() => \App\Services\CelebrationCatalogService::clearCache());
    }
}
