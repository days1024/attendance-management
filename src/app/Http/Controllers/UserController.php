<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Models\User;
use App\Models\Attendance;
use App\Models\BreakTime;
use App\Models\AttendanceRequest;
use App\Models\RequestBreakTime;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;
use Carbon\CarbonPeriod;

class UserController extends Controller
{
    //
    public function index(Request $request)
{
    $month = $request->month
        ? Carbon::parse($request->month)
        : now();

    $attendances = Attendance::where('user_id', auth()->id())
        ->whereYear('work_date', $month->year)
        ->whereMonth('work_date', $month->month)
        ->get()
        ->keyBy(function ($item) {
            return $item->work_date->format('Y-m-d');
        });

    $dates = CarbonPeriod::create(
        $month->copy()->startOfMonth(),
        $month->copy()->endOfMonth()
    );

    return view('user.index', compact(
        'month',
        'dates',
        'attendances'
    ));
}

  public function login(LoginRequest $request) {

    $credentials = $request->only('email','password');

    if (!Auth::attempt($credentials)) {
        throw ValidationException::withMessages([
            'email' => ['ログイン情報が登録されていません'],
        ]);
    }

    $request->session()->regenerate();

     if (!Auth::user()->hasVerifiedEmail()) {
        return redirect('/email/verify');
    }

    return redirect()->intended('/attendance');

}

    public function create()
  {
    return view('user.create');
  }

  public function action(Request $request)
{
     $status = $request->status;

    $attendance = Attendance::where('user_id', auth()->id())
        ->whereDate('work_date', now())
        ->first();

    switch ($status) {

        case 'clock_in':
            $attendance = Attendance::firstOrCreate(
                 [
                    'user_id' => auth()->id(),
                    'work_date' => now()->toDateString(),
                    ],
                [
                    'status' => 'working',
                    'clock_in' => now(),
                ]
                );
            break;

        case 'break_start':
            $attendance->update([
                'status' => 'on_break',
            ]);
            BreakTime::create([
                'attendance_id' => $attendance->id,
                'break_start' => now(),
            ]);
            break;

        case 'break_end':
            $attendance->update([
                'status' => 'working',
            ]);
            $break = BreakTime::where('attendance_id', $attendance->id)
            ->whereNull('break_end')
            ->latest()
            ->first();

            if ($break) {
            $break->update([
            'break_end' => now(),
            ]);
            }
            break;

        case 'clock_out':
            $attendance->update([
                'status' => 'finished',
                'clock_out' => now(),
            ]);
            break;
    }

    return view('user.create', compact('attendance'));
}

    public function detail($attendance_id)
  {
    $attendance = Attendance::with('user','breakTimes')->findOrFail($attendance_id); 

    $hasRequest = AttendanceRequest::where('attendance_id', $attendance_id)
        ->where('status', 'pending')
        ->exists();

    $reason = AttendanceRequest::where('attendance_id', $attendance_id)
    ->where('status', 'pending')
    ->value('reason');
        
    return view('user.show',compact('attendance','hasRequest','reason'));
  }


  public function update(Request $request,$attendance_id)
{
    $req = AttendanceRequest::create([
        'user_id' => auth()->id(),
        'attendance_id' => $request->attendance_id,
        'request_clock_in' => $request->request_clock_in,
        'request_clock_out' => $request->request_clock_out,
        'request_break_start'=>$request->request_break_start,
        'request_break_end'=>$request->request_break_end,
        'reason' => $request->reason,
        'status' => 'pending',
    ]);

    foreach ($request->request_break_start as $index => $start) {
        RequestBreakTime::create([
            'attendance_request_id' => $req->id,
            'request_break_start' => $start,
            'request_break_end' => $request->request_break_end[$index],
        ]);
    }

    return redirect()->back();
}

public function request()
  {
    $requests = AttendanceRequest::with([
        'attendance.user'
    ])
    ->whereHas('attendance', function ($q) {
        $q->where('user_id', auth()->id());
    })
    ->latest()
    ->get();

    return view('user.request', compact('requests'));
  }
}
