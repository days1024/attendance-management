<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceRequest;
use App\Models\RequestBreakTime;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class AdminController extends Controller
{
    //
    public function index(Request $request)
{
    $day = $request->day
        ? Carbon::parse($request->day)
        : now();

    $users = User::with(['attendance' => function ($query) use ($day) {
    $query->whereDate('work_date', $day);
    }])->get();

    return view('admin.index', compact(
        'day',
        'users'
    ));
}
}
