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
        // New fields
        'icon',
        'bg_color',
        'slider_image',
        'dropdown_image',
        'dropdown_badge',
        'nav_slug',
    ];

    protected $casts = [
        'active'        => 'boolean',
        'count'         => 'integer',
        'display_order' => 'integer',
    ];

    public function packages(): HasMany
    {
        return $this->hasMany(Package::class);
    }

    public function subcategories(): HasMany
    {
        return $this->hasMany(Subcategory::class)->orderBy('display_order')->orderBy('id');
    }

    public function activeSubcategories(): HasMany
    {
        return $this->hasMany(Subcategory::class)->where('is_active', true)->orderBy('display_order')->orderBy('id');
    }

    public function scopeActive($query)
    {
        return $query->where('active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order')->orderBy('id');
    }

    /**
     * Returns the correct nav_slug for use in URL params (cat-birthdays, etc.)
     */
    public function getNavSlugAttribute($value): string
    {
        if ($value) return $value;
        // Fallback: derive from title slug
        $base = \Illuminate\Support\Str::slug($this->title);
        return 'cat-' . $base;
    }

    /**
     * The slider image (for homepage occasion card).
     */
    public function getSliderImageAttribute($value): ?string
    {
        return $value ?: null;
    }

    /**
     * The dropdown image (for header mega-menu right panel).
     */
    public function getDropdownImageAttribute($value): ?string
    {
        return $value ?: null;
    }
}
