<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsPromiseBanner extends Model
{
    use HasFactory;

    protected $table = 'cms_promise_banners';

    protected $fillable = [
        'image',
        'title',
        'link',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
