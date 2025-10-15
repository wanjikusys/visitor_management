<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class VisitorVisit extends Model
{
    use HasFactory;

    protected $fillable = [
        'visitor_id',
        'host_id',
        'visit_purpose',
        'visit_notes',
        'badge_number',
        'qr_code',
        'check_in_time',
        'expected_checkout_time',
        'actual_checkout_time',
        'status',
        'temperature',
        'health_screening_passed',
        'checkout_notes',
        'checked_out_by',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'expected_checkout_time' => 'datetime',
        'actual_checkout_time' => 'datetime',
        'health_screening_passed' => 'boolean',
    ];

    public function visitor()
    {
        return $this->belongsTo(Visitor::class);
    }

    public function host()
    {
        return $this->belongsTo(User::class, 'host_id');
    }

    public function checkedOutBy()
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    public function vehicleVisit()
    {
        return $this->hasOne(VehicleVisit::class);
    }

    public function assetCheckouts()
    {
        return $this->hasMany(AssetCheckout::class);
    }
}
