<?php

namespace App\Models\Hmis;

use App\Models\User;
use Illuminate\Database\Eloquent\Model;

class HmisVehicle extends Model
{
    protected $table = 'hmis_vehicles';

    protected $fillable = [
        'card_no',
        'driver_name',
        'registration',
        'phone_number',
        'visit_purpose',
        'passengers',
        'time_in',
        'time_out',
        'checked_in',
        'notes',
        'checked_in_by',
        'checked_out_by',
    ];

    protected $casts = [
        'time_in' => 'datetime',
        'time_out' => 'datetime',
        'checked_in' => 'boolean',
    ];

    public function checkedInBy()
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function checkedOutBy()
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }
}
