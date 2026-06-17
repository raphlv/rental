<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Rental extends Model
{
    use HasFactory;

    protected $fillable = [
        'unit_id',
        'customer_name',
        'duration',
        'start_time',
        'end_time',
        'payment_method',
        'photo_proof',
        'status',
        'total_price'
    ];

    protected $casts = [
        'start_time' => 'datetime',
        'end_time' => 'datetime',
    ];

    public function unit()
    {
        return $this->belongsTo(Unit::class);
    }
}
