<?php

namespace App\Models\Hmis;

use Illuminate\Database\Eloquent\Model;

class OpdVisit extends Model
{
    protected $connection = 'hmis';
    protected $table = 'dbo.consultationheader';
    protected $primaryKey = 'PatientNumber';
    public $incrementing = false;
    protected $keyType = 'string';
    public $timestamps = false;

    protected $fillable = [
        'BranchId',
        'PatientName',
        'PatientNumber',
        'DateTimeIn',
        'PatientStatus',
    ];

    protected $casts = [
        'DateTimeIn' => 'datetime',
    ];

    public function customerInformation()
    {
        return $this->hasOne(CustomerInformation::class, 'PatientNumber', 'PatientNumber');
    }

    public function scopeKijabe($query)
    {
        return $query->where('BranchId', 'KIJABE');
    }

    public function scopeLast24Hours($query)
    {
        return $query->whereRaw('DateTimeIn >= DATEADD(HOUR, -24, GETDATE())');
    }

    public function scopeLatestPerPatient($query)
    {
        $subQuery = static::selectRaw('
            PatientNumber,
            MAX(DateTimeIn) as MaxDateTime
        ')
        ->groupBy('PatientNumber');

        return $query->joinSub($subQuery, 'latest', function ($join) {
            $join->on('consultationheader.PatientNumber', '=', 'latest.PatientNumber')
                 ->on('consultationheader.DateTimeIn', '=', 'latest.MaxDateTime');
        });
    }
}
