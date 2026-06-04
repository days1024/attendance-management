<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttendanceRequest extends Model
{
    use HasFactory;

    protected $fillable = [
        'attendance_id',
        'request_clock_in',
        'request_clock_out',
        'reason',
        'status',
    ];

    

public function breakTimes()
{
    return $this->hasMany(RequestBreakTime::class, 'request_id');
}

public function attendance()
{
    return $this->belongsTo(Attendance::class);
}

public function getStatusLabelAttribute()
{
    return match ($this->status) {
        'pending' => '承認待ち',
        'approved' => '承認済み',
        default => $this->status,
    };
}


public function requestBreakTimes()
{
    return $this->hasMany(RequestBreakTime::class);
}
}
