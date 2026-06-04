<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class ClockInTest extends TestCase
{
    use RefreshDatabase;
    public function test_clock_in_button_works(): void
{   
    $user = User::factory()->create();

    $response = $this->actingAs($user)->post('/attendance', [
        'status' => 'clock_in',
    ]);

    $this->assertDatabaseHas('attendances', [
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'working',
    ]);
}

    public function test_user_cannot_clock_in_twice_a_day(): void
{
    $user = User::factory()->create();

    Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
        'clock_in' => now()->setTime(9, 0),
        'clock_out' => now()->setTime(18, 0),
    ]);

    $response = $this->actingAs($user)->get('/attendance');

    $response->assertStatus(200);

    $response->assertDontSee('出勤');
}

    public function test_clock_in_time_is_displayed_in_attendance_list(): void
{
    $user = User::factory()->create();

    $attendanceDate = now()->toDateString(); 

    Attendance::create([
        'user_id' => $user->id,
        'work_date' => $attendanceDate,
        'status' => 'working',
        'clock_in' => now()->setTime(9, 0),
    ]);

    $response = $this->actingAs($user)->get('/attendance/list');

    $response->assertStatus(200);

    $week = ['日','月','火','水','木','金','土'];

    $displayDate = now()->format('m/d')
        . '(' . $week[now()->dayOfWeek] . ')';

    $response->assertSee($displayDate);
    $response->assertSee('09:00');
}
}
