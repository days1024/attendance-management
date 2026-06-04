<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class BreakTimeTest extends TestCase
{
    use RefreshDatabase;
    public function test_break_start_changes_status_to_on_break(): void
{
    $user = User::factory()->create();

    Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'working',
        'clock_in' => now()->setTime(9, 0),
    ]);

    $response = $this->actingAs($user)->get('/attendance');

    $response->assertStatus(200);
    $response->assertSee('休憩入');

    $response = $this->actingAs($user)->post('/attendance', [
        'status' => 'break_start',
    ]);

    $response->assertStatus(200);

    $response = $this->actingAs($user)->get('/attendance');

    $response->assertSee('休憩中');
}

   public function test_break_start_button_is_visible_after_break_cycle(): void
{
    $user = User::factory()->create();

    Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'working',
        'clock_in' => now()->setTime(9, 0),
    ]);

    $this->actingAs($user)->post('/attendance', [
        'status' => 'break_start',
    ]);

    $this->actingAs($user)->post('/attendance', [
        'status' => 'break_end',
    ]);

    $response = $this->actingAs($user)->get('/attendance');

    $response->assertStatus(200);

    $response->assertSee('休憩入');
}

    public function test_break_end_button_changes_status_to_working(): void
{
    $user = User::factory()->create();

    Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'working',
        'clock_in' => now()->setTime(9, 0),
    ]);

    $this->actingAs($user)->post('/attendance', [
        'status' => 'break_start',
    ]);

    $response = $this->actingAs($user)->get('/attendance');
    $response->assertStatus(200);
    $response->assertSee('休憩戻');


    $this->actingAs($user)->post('/attendance', [
        'status' => 'break_end',
    ]);

    $response = $this->actingAs($user)->get('/attendance');

    $response->assertStatus(200);

    $response->assertSee('出勤中');
    $response->assertSee('休憩入');
}

    public function test_break_end_button_is_shown_after_second_break_start(): void
{
    $user = User::factory()->create();

    Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'working',
        'clock_in' => now()->setTime(9, 0),
    ]);

    $this->actingAs($user)->post('/attendance', [
        'status' => 'break_start',
    ]);

    $this->actingAs($user)->post('/attendance', [
        'status' => 'break_end',
    ]);

    $this->actingAs($user)->post('/attendance', [
        'status' => 'break_start',
    ]);

    $response = $this->actingAs($user)->get('/attendance');

    $response->assertStatus(200);


    $response->assertSee('休憩戻');

    $response->assertDontSee('休憩入');
}

    public function test_break_time_is_saved_and_displayed_in_list(): void
{
    $user = User::factory()->create();

    $this->actingAs($user);

    Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'working',
        'clock_in' => now()->setTime(9, 0),
    ]);

    $this->post('/attendance', [
        'status' => 'break_start',
    ]);

    $this->post('/attendance', [
        'status' => 'break_end',
    ]);

    $response = $this->get('/attendance/list');

    $response->assertStatus(200);

    $week = ['日','月','火','水','木','金','土'];

    $date = now()->format('m/d') . '(' . $week[now()->dayOfWeek] . ')';

    $response->assertSee($date);

    $response->assertSee(':');
}
}
