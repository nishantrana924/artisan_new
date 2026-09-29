<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Booking extends Model
{
    use HasFactory;

    public $incrementing = false;
    protected $keyType = 'string';

    protected $fillable = [
        'id',
        'customer_id',
        'package_id',
        'customer_name',
        'customer_mobile',
        'customer_whatsapp',
        'customer_email',
        'package_name',
        'category_name',
        'tier_name',
        'event_date',
        'event_time',
        'guest_count',
        'address',
        'landmark',
        'area',
        'city',
        'state',
        'pincode',
        'notes',
        'package_price',
        'surcharge_amount',
        'total_amount',
        'status',
        'notifications_sent',
    ];

    protected $casts = [
        'notifications_sent' => 'array',
        'guest_count' => 'integer',
        'package_price' => 'decimal:2',
        'surcharge_amount' => 'decimal:2',
        'total_amount' => 'decimal:2',
    ];

    public function customer(): BelongsTo
    {
        return $this->belongsTo(Customer::class);
    }

    public function package(): BelongsTo
    {
        return $this->belongsTo(Package::class);
    }
}
