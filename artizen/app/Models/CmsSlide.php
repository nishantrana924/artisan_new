<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsSlide extends Model
{
    use HasFactory;

    protected $fillable = [
        'badge',
        'title',
        'description',
        'image',
        'button1_text',
        'button1_link',
        'button2_text',
        'button2_link',
        'display_order',
        'is_active',
    ];

    protected $casts = [
        'display_order' => 'integer',
        'is_active' => 'boolean',
    ];
}
