<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class UserAttendanceShowTest extends TestCase
{
    use RefreshDatabase;
   public function test_user_name_is_displayed_on_attendance_detail_page(): void
{
    $user = User::factory()->create([
        'name' => '山田太郎',
    ]);

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
    ]);

    $response = $this->actingAs($user)->get(
        '/attendance/detail/' . $attendance->id
    );

    $response->assertStatus(200);

    $response->assertSee('山田太郎');
}

   public function test_selected_date_is_displayed_on_attendance_detail_page(): void
{
    $user = User::factory()->create();

    $date = now();

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'work_date' => $date->toDateString(),
        'status' => 'finished',
    ]);

    $response = $this->actingAs($user)->get(
        '/attendance/detail/' . $attendance->id
    );

    $response->assertStatus(200);

    $response->assertSee($date->format('Y年'));

    $response->assertSee($date->format('n月j日'));
}

    public function test_clock_in_and_clock_out_times_are_displayed(): void
{
    $user = User::factory()->create();

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
        'clock_in' => now()->setTime(9, 0),
        'clock_out' => now()->setTime(18, 0),
    ]);

    $response = $this->actingAs($user)->get(
        '/attendance/detail/' . $attendance->id
    );

    $response->assertStatus(200);

    $response->assertSee('09:00');
    $response->assertSee('18:00');
}

    public function test_break_times_are_displayed(): void
{
    $user = User::factory()->create();

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
    ]);

    $attendance->breakTimes()->create([
        'break_start' => now()->setTime(12, 0),
        'break_end' => now()->setTime(13, 0),
    ]);

    $response = $this->actingAs($user)->get(
        '/attendance/detail/' . $attendance->id
    );

    $response->assertStatus(200);

    $response->assertSee('12:00');
    $response->assertSee('13:00');
}

}
