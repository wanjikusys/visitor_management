<?php

namespace App\Http\Controllers\Hmis;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class HmisDevicesController extends Controller
{
    public function index()
    {
        return view('hmis.devices.index');
    }
}
