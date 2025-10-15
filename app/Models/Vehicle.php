<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Vehicle extends Model
{
    protected $table = 'vehicles';

    protected $fillable = [
        'plate_number',
        'make',
        'model',
        'color',
        'year',
        'vehicle_type',
        'photo_path',
        'is_blacklisted',
        'blacklist_reason',
        'blacklisted_at',
    ];

    protected $casts = [
        'is_blacklisted' => 'boolean',
        'blacklisted_at' => 'datetime',
    ];

    public function vehicleVisits(): HasMany
    {
        return $this->hasMany(VehicleVisit::class);
    }

    public function currentVisit(): HasOne
    {
        return $this->hasOne(VehicleVisit::class)
            ->whereNull('exit_time')
            ->latest();
    }
}
