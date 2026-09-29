<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'mobile',
        'whatsapp',
        'email',
        'address',
        'area',
        'city',
        'state',
        'pincode',
        'total_bookings',
        'total_spend',
    ];

    protected $casts = [
        'total_bookings' => 'integer',
        'total_spend' => 'decimal:2',
    ];

    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }
}
