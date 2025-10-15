<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Asset extends Model
{
    use HasFactory;

    protected $fillable = [
        'asset_category_id',
        'asset_code',
        'name',
        'description',
        'serial_number',
        'barcode',
        'qr_code',
        'purchase_price',
        'purchase_date',
        'status',
        'location',
        'photo_path',
        'specifications',
    ];

    protected $casts = [
        'purchase_date' => 'date',
        'purchase_price' => 'decimal:2',
    ];

    public function category()
    {
        return $this->belongsTo(AssetCategory::class, 'asset_category_id');
    }

    public function checkouts()
    {
        return $this->hasMany(AssetCheckout::class);
    }

    public function currentCheckout()
    {
        return $this->hasOne(AssetCheckout::class)->where('status', 'checked_out')->latest();
    }
}
