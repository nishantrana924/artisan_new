<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Testimonial extends Model
{
    use HasFactory;

    protected $table = 'testimonials';
    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'author',
        'email',
        'location',
        'event_type',
        'package_slug',
        'review',
        'rating',
        'avatar',
        'is_verified',
        'is_active',
        'show_on_home',
        'show_on_reviews_page',
        'show_on_event_details',
        'sort_order',
    ];

    protected $casts = [
        'rating'                => 'integer',
        'is_verified'           => 'boolean',
        'is_active'             => 'boolean',
        'show_on_home'          => 'boolean',
        'show_on_reviews_page'  => 'boolean',
        'show_on_event_details' => 'boolean',
        'sort_order'            => 'integer',
    ];

    /**
     * Scope: Active reviews only.
     */
    public function scopeActive(Builder $query): Builder
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope: Reviews eligible for Homepage.
     */
    public function scopeForHome(Builder $query): Builder
    {
        return $query->where('is_active', true)->where('show_on_home', true);
    }

    /**
     * Scope: Reviews eligible for the dedicated Reviews page (/reviews).
     */
    public function scopeForReviewsPage(Builder $query): Builder
    {
        return $query->where('is_active', true)->where('show_on_reviews_page', true);
    }

    /**
     * Scope: Reviews eligible for Event / Package details page (/event/{slug}).
     */
    public function scopeForEventDetails(Builder $query, ?string $packageSlug = null): Builder
    {
        $q = $query->where('is_active', true)->where('show_on_event_details', true);
        if (!empty($packageSlug)) {
            $q->where('package_slug', $packageSlug);
        }
        return $q;
    }

    /**
     * Scope: Display order sorting.
     */
    public function scopeOrdered(Builder $query): Builder
    {
        return $query->orderBy('sort_order', 'asc')->orderBy('created_at', 'desc');
    }

    /**
     * Relationship to related package by slug, if exists.
     */
    public function package()
    {
        return $this->belongsTo(Package::class, 'package_slug', 'slug');
    }
}
