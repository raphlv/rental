<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'customer_id',
        'customer_name',
        'customer_phone',
        'customer_photo',
        'start_time',
        'end_time',
        'duration_hours',
        'price_per_hour',
        'total_price',
        'payment_method',
        'payment_status',
        'status',
        'notes',
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }

    public function customer()
    {
        return $this->belongsTo(Customer::class);
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->customer_photo) {
            if (str_starts_with($this->customer_photo, 'data:image')) {
                return $this->customer_photo;
            }
            return asset('storage/' . $this->customer_photo);
        }
        if ($this->customer && $this->customer->photo_path) {
            return $this->customer->photo_url;
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->customer_name) . '&background=8b5cf6&color=fff';
    }
}
