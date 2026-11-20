<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Faq extends Model
{
    use HasFactory;

    protected $fillable = [
        'question',
        'answer',
        'category',
        'is_active',
        'show_on_homepage',
        'sort_order',
        'show_on_home',
        'display_order',
    ];

    protected $casts = [
        'is_active'        => 'boolean',
        'show_on_homepage' => 'boolean',
        'sort_order'       => 'integer',
    ];

    protected $appends = [
        'show_on_home',
        'display_order',
    ];

    /* =========================================================================
     * SCOPES
     * ========================================================================= */

    /**
     * Scope a query to only include active FAQs.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to only include FAQs enabled for the homepage.
     */
    public function scopeForHomepage($query)
    {
        if (\Illuminate\Support\Facades\Schema::hasColumn('faqs', 'show_on_homepage')) {
            return $query->where('show_on_homepage', true);
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('faqs', 'show_on_home')) {
            return $query->where('show_on_home', true);
        }

        return $query;
    }

    /**
     * Scope a query to order FAQs by sort_order and id.
     */
    public function scopeOrdered($query)
    {
        if (\Illuminate\Support\Facades\Schema::hasColumn('faqs', 'sort_order')) {
            return $query->orderBy('sort_order', 'asc')->orderBy('id', 'asc');
        }

        if (\Illuminate\Support\Facades\Schema::hasColumn('faqs', 'display_order')) {
            return $query->orderBy('display_order', 'asc')->orderBy('id', 'asc');
        }

        return $query->orderBy('id', 'asc');
    }

    /* =========================================================================
     * BACKWARD COMPATIBILITY ACCESSORS & MUTATORS
     * ========================================================================= */

    public function getShowOnHomeAttribute(): bool
    {
        return (bool) ($this->attributes['show_on_homepage'] ?? false);
    }

    public function setShowOnHomeAttribute($value): void
    {
        $this->attributes['show_on_homepage'] = (bool) $value;
    }

    public function getDisplayOrderAttribute(): int
    {
        return (int) ($this->attributes['sort_order'] ?? 0);
    }

    public function setDisplayOrderAttribute($value): void
    {
        $this->attributes['sort_order'] = (int) $value;
    }
}

