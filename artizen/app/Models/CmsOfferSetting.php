<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CmsOfferSetting extends Model
{
    use HasFactory;

    protected $table = 'cms_offer_settings';

    protected $fillable = [
        'section_title',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];
}
