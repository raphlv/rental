<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class RainfallData extends Model
{
    use HasFactory;

    protected $table = 'rainfall_data';

    protected $fillable = [
        'tanggal',
        'tavg',
        'rh_avg',
        'rr',
        'class_actual'
    ];

    protected $casts = [
        'tanggal' => 'date',
        'tavg' => 'double',
        'rh_avg' => 'double',
        'rr' => 'double',
        'class_actual' => 'integer'
    ];
}
