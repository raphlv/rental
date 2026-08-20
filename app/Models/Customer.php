<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Customer extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'phone',
        'nik_ktp',
        'address',
        'photo_path',
        'notes',
    ];

    public function rentals()
    {
        return $table = $this->hasMany(Rental::class);
    }

    public function getPhotoUrlAttribute()
    {
        if ($this->photo_path) {
            if (str_starts_with($this->photo_path, 'data:image')) {
                return $this->photo_path;
            }
            return asset('storage/' . $this->photo_path);
        }
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=8b5cf6&color=fff';
    }
}
