<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\AttendanceRequest as AttendanceRequestForm;
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

     public function login(LoginRequest $request)
{
    $credentials = $request->only('email', 'password');

    // admin guardで認証
    if (!Auth::guard('admin')->attempt($credentials)) {
        throw ValidationException::withMessages([
            'email' => ['ログイン情報が登録されていません'],
        ]);
    }

    $request->session()->regenerate();

    // adminユーザー取得
    $admin = Auth::guard('admin')->user();


    return redirect('/admin/attendance/list');
}

    public function logout(Request $request)
  {
     Auth::guard('admin')->logout();

    return redirect('/admin/login');
  }

    public function create()
  {
    return view('user.create');
  }

  
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

    public function staff()
  {
    $users = User::all();
    return view('admin.staff',compact('users'));
  }

  public function attendance( Request $request,$id)
  {
    $month = $request->month
        ? Carbon::parse($request->month)
        : now();

    $user = User::findOrFail($id);

    $attendances = Attendance::with('user','breakTimes')      ->where('user_id', $id)
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

    return view('admin.attendance', compact(
        'month',
        'dates',
        'attendances',
        'user' 
    ));
}

    public function detail($id)
  {
    $attendance = Attendance::with('user','breakTimes')->findOrFail($id); 

    $hasRequest = AttendanceRequest::where('attendance_id', $id)
        ->where('status', 'pending')
        ->exists();

    $reason = AttendanceRequest::where('attendance_id', $id)
    ->where('status', 'pending')
    ->value('reason');
        
    return view('admin.show',compact('attendance','hasRequest','reason'));
  }


     public function update(AttendanceRequestForm $request, $id)
{
    $req = AttendanceRequest::create([
        'user_id' => auth()->id(),
        'attendance_id' => $id, 
        'request_clock_in' => $request->request_clock_in,
        'request_clock_out' => $request->request_clock_out,
        'reason' => $request->reason,
        'status' => 'pending',
    ]);

    $starts = $request->request_break_start ?? [];
    $ends   = $request->request_break_end ?? [];

    foreach ($starts as $index => $start) {
        $end = $ends[$index] ?? null;
        if (empty($start) || empty($end)) {
        continue;
        }
            RequestBreakTime::create([
                'attendance_request_id' => $req->id,
                'request_break_start' => $start,
                'request_break_end' => $end,
            ]);
        }
    
    
    return redirect()->back()->withInput();
}

    



  public function showApprove($id)
{
    $attendanceRequest = AttendanceRequest::with('requestBreakTimes')
    ->findOrFail($id);

    $attendance = Attendance::with('user', 'breakTimes')
    ->findOrFail($attendanceRequest->attendance_id);
    $hasRequest = AttendanceRequest::where('attendance_id', $attendance->id)
    ->where('status', 'approved')
    ->exists();
    
    $reason = $attendanceRequest?->reason;

    return view('admin.approve', compact(
        'attendance',
        'attendanceRequest',
        'hasRequest',
        'reason'
    ));
}


    public function approve(Request $request, $attendance_correct_request_id)
{
    $attendanceRequest = AttendanceRequest::with('requestBreakTimes')
        ->findOrFail($attendance_correct_request_id);

    $attendance = Attendance::findOrFail(
        $attendanceRequest->attendance_id
    );

    $attendance->update([
    'clock_in' => $attendance->work_date->format('Y-m-d') . ' ' . $attendanceRequest->request_clock_in,
    'clock_out' => $attendance->work_date->format('Y-m-d') . ' ' . $attendanceRequest->request_clock_out,
    ]);

    BreakTime::where('attendance_id', $attendance->id)->delete();

    foreach ($attendanceRequest->requestBreakTimes as $break) {
    BreakTime::create([
    'attendance_id' => $attendance->id,
    'break_start' => $attendance->work_date->format('Y-m-d') . ' ' . $break->request_break_start,
    'break_end' => $attendance->work_date->format('Y-m-d') . ' ' . $break->request_break_end,
    ]);
    }

    $attendanceRequest->update([
        'status' => 'approved',
    ]);


    return redirect()->back();
}

    public function exportCsv(Request $request, $id)
{
    $month = $request->month;

    $start = Carbon::parse($month)->startOfMonth();
    $end = Carbon::parse($month)->endOfMonth();

    $attendances = Attendance::where('user_id', $id)
        ->whereBetween('work_date', [$start, $end])
        ->get()
        ->keyBy(function ($attendance) {
            return Carbon::parse($attendance->work_date)
                ->format('Y-m-d');
        });

    $dates = collect();

    for ($date = $start->copy(); $date <= $end; $date->addDay()) {
        $dates->push($date->copy());
    }

    $days = ['日','月','火','水','木','金','土'];

    $fileName = 'attendance_' . $month . '.csv';

    $headers = [
        'Content-Type' => 'text/csv',
        'Content-Disposition' => "attachment; filename={$fileName}",
    ];

    $callback = function () use ($dates, $attendances, $days) {

        $stream = fopen('php://output', 'w');

        fwrite($stream, "\xEF\xBB\xBF");

        fputcsv($stream, [
            '日付',
            '出勤',
            '退勤',
            '休憩',
            '合計',
        ]);

        foreach ($dates as $date) {

            $attendance = $attendances[$date->format('Y-m-d')] ?? null;

            fputcsv($stream, [
                $date->format('m/d') . '(' . $days[$date->dayOfWeek] . ')',

                $attendance?->clock_in?->format('H:i') ?? '-',

                $attendance?->clock_out?->format('H:i') ?? '-',

                $attendance?->total_break_time ?? '-',

                $attendance?->working_time ?? '-',
            ]);
        }

        fclose($stream);
    };

    return response()->stream($callback, 200, $headers);
}
}
