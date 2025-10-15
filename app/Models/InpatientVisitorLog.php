<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InpatientVisitorLog extends Model
{
    protected $fillable = [
        'patient_number',
        'patient_name',
        'ward_number',
        'bed_number',
        'visitor_name',
        'visitor_id_number',
        'visitor_phone',
        'relationship',
        'check_in_time',
        'check_out_time',
        'checked_in_by',
        'checked_out_by',
        'notes',
    ];

    protected $casts = [
        'check_in_time' => 'datetime',
        'check_out_time' => 'datetime',
    ];

    public function checkedInBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_in_by');
    }

    public function checkedOutBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }
}
