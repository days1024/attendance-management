<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class AttendanceStatusTest extends TestCase
{
    use RefreshDatabase;
    public function test_status_is_displayed_as_off_duty(): void
{
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/attendance');

    $response->assertStatus(200);

    $response->assertSee('勤務外');
}

    public function test_status_is_displayed_as_working(): void
{
    $user = User::factory()->create();

    Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'working',
        'clock_in' => now(),
    ]);

    $response = $this->actingAs($user)->get('/attendance');

    $response->assertStatus(200);

    $response->assertSee('出勤中');
}

    public function test_status_is_displayed_as_on_break(): void
{
    $user = User::factory()->create();

    Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'on_break',
        'clock_in' => now(),
    ]);

    $response = $this->actingAs($user)->get('/attendance');

    $response->assertStatus(200);

    $response->assertSee('休憩中');
}

    public function test_status_is_displayed_as_finished(): void
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

    $response->assertSee('退勤済');
}
}
