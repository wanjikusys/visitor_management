<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Visitor extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'id_number',
        'phone_number',
        'email',
        'company',
        'photo_path',
        'id_type',
        'address',
        'is_blacklisted',
        'blacklist_reason',
        'blacklisted_at',
    ];

    protected $casts = [
        'is_blacklisted' => 'boolean',
        'blacklisted_at' => 'datetime',
    ];

    public function visits()
    {
        return $this->hasMany(VisitorVisit::class);
    }

    public function activeVisit()
    {
        return $this->hasOne(VisitorVisit::class)->where('status', 'active')->latest();
    }
}
