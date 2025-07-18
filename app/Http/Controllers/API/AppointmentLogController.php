<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Utility\LogUtility;

use Illuminate\Http\Request;

class AppointmentLogController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        // return LogUtility::createAppointmentLog(['shift' => 2]);
    }
}
