<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\File;

class SerialNumberController extends Controller
{
    public function getSerialNumber($serial_number)
    {
        return view('pages.serial-number');
    }
}