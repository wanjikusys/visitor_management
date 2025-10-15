<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Carer extends Model
{
    protected $fillable = [
        'baby_admitted',
        'patient_name',
        'patient_number',
        'ward',
        'bed_number',
        'carer_name',
        'carer_contact',
        'carer_id_number',
        'relationship',
        'notes',
        'date_in',
        'date_out',
        'registered_by',
    ];

    protected $casts = [
        'date_in' => 'datetime',
        'date_out' => 'datetime',
    ];

    public function registeredBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'registered_by');
    }
}
