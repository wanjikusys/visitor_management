<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ParkingZone extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'description',
        'total_slots',
        'available_slots',
        'zone_type',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    public function vehicleVisits()
    {
        return $this->hasMany(VehicleVisit::class);
    }
}
