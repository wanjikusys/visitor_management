<?php

namespace App\Models\Hmis;

use Illuminate\Database\Eloquent\Model;

class DischargeRequest extends Model
{
    protected $connection = 'hmis';
    protected $table = 'dbo.BedOccupancyDetail';
    public $timestamps = false;
}
