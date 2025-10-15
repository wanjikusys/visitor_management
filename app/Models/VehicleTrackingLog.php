<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleTrackingLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_visit_id',
        'user_id',
        'event_type',
        'location',
        'notes',
        'camera_id',
        'photo_path',
        'event_time',
    ];

    protected $casts = [
        'event_time' => 'datetime',
    ];

    public function vehicleVisit()
    {
        return $this->belongsTo(VehicleVisit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
