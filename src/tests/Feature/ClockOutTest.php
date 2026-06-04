<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class ClockOutTest extends TestCase
{
    use RefreshDatabase;
    public function test_clock_out_changes_status_to_finished(): void
{
    $user = User::factory()->create();

    Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'working',
        'clock_in' => now()->setTime(9, 0),
    ]);

    $this->actingAs($user)->post('/attendance', [
        'status' => 'clock_out',
    ]);

    $response = $this->actingAs($user)->get('/attendance');

    $response->assertStatus(200);

    $response->assertSee('退勤済');

}

    public function test_clock_out_time_is_displayed_in_attendance_list(): void
{
    $user = User::factory()->create();

    $this->actingAs($user);

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'working',
        'clock_in' => now()->setTime(9, 0),
    ]);

    $this->post('/attendance', [
        'status' => 'clock_out',
    ]);

    $response = $this->get('/attendance/list');

    $response->assertStatus(200);

    $week = ['日','月','火','水','木','金','土'];
    $date = now()->format('m/d') . '(' . $week[now()->dayOfWeek] . ')';

    $response->assertSee($date);

    $response->assertSee(':');
}
}
