<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AssetCheckout extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_id',
        'visitor_visit_id',
        'user_id',
        'checked_out_by',
        'approved_by',
        'checkout_time',
        'expected_return_time',
        'actual_return_time',
        'checkout_condition',
        'return_condition',
        'checkout_notes',
        'return_notes',
        'status',
        'returned_by',
        'checkout_signature',
        'return_signature',
    ];

    protected $casts = [
        'checkout_time' => 'datetime',
        'expected_return_time' => 'datetime',
        'actual_return_time' => 'datetime',
    ];

    public function asset()
    {
        return $this->belongsTo(Asset::class);
    }

    public function visitorVisit()
    {
        return $this->belongsTo(VisitorVisit::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function checkedOutBy()
    {
        return $this->belongsTo(User::class, 'checked_out_by');
    }

    public function approvedBy()
    {
        return $this->belongsTo(User::class, 'approved_by');
    }

    public function returnedBy()
    {
        return $this->belongsTo(User::class, 'returned_by');
    }
}
