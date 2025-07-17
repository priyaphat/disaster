<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Resources\AppointmentLogResource;
use App\Http\Utility\LogUtility;
use App\Models\Appointment;
use App\Models\AppointmentLog;
use App\Models\Shelter;
use App\Models\Team;
use Carbon\Carbon;;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AppointmentLogController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        LogUtility::createAppointmentLog(['shift' => 2]);
    }
}
