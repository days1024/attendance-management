<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attendance extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'work_date',
        'status',
        'clock_in',
        'clock_out',
    ];

    protected $casts = [
    'work_date' => 'date',
    'clock_in' => 'datetime',
    'clock_out' => 'datetime',
];


public function breakTimes()
{
    return $this->hasMany(BreakTime::class, 'attendance_id');
}

public function user()
{
    return $this->belongsTo(User::class);
}

public function getTotalBreakMinutesAttribute()
{
    return $this->breakTimes->sum('break_minutes');
}

public function getTotalBreakTimeAttribute()
{
    $minutes = $this->breakTimes->sum('break_minutes');

    return sprintf(
        '%02d:%02d',
        floor($minutes / 60),
        $minutes % 60
    );
}

public function getWorkingMinutesAttribute()
{
    if (!$this->clock_in || !$this->clock_out) {
        return 0;
    }

    return $this->clock_in->diffInMinutes($this->clock_out);
}

public function getWorkingTimeAttribute()
{
    $minutes = $this->working_minutes;

    return sprintf(
        '%02d:%02d',
        floor($minutes / 60),
        $minutes % 60
    );
}
}
