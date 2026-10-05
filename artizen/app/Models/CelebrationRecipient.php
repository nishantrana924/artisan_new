<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CelebrationRecipient extends Model
{
    use HasFactory;

    protected $table = 'celebration_recipients';

    protected $fillable = [
        'name',
        'slug',
        'image',
        'category_slug',
        'custom_url',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'is_active'     => 'boolean',
        'display_order' => 'integer',
    ];

    /**
     * Scope for active/published items.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope ordered by sort position.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('display_order', 'asc')->orderBy('id', 'asc');
    }

    /**
     * Get computed destination target URL.
     */
    public function getTargetUrlAttribute(): string
    {
        return route('events.index', ['for' => $this->slug]);
    }
}
