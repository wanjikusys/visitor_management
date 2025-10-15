<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VehicleVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'vehicle_id',
        'visitor_visit_id',
        'parking_zone_id',
        'parking_slot',
        'entry_time',
        'exit_time',
        'duration_minutes',
        'entry_notes',
        'exit_notes',
        'status',
    ];

    protected $casts = [
        'entry_time' => 'datetime',
        'exit_time' => 'datetime',
    ];

    public function vehicle()
    {
        return $this->belongsTo(Vehicle::class);
    }

    public function visitorVisit()
    {
        return $this->belongsTo(VisitorVisit::class);
    }

    public function parkingZone()
    {
        return $this->belongsTo(ParkingZone::class);
    }

    public function trackingLogs()
    {
        return $this->hasMany(VehicleTrackingLog::class);
    }
}
