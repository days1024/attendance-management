<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Admin;
use App\Models\BreakTime;

class AdminAttendanceDetailTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_can_see_correct_attendance_detail(): void
{
    $admin = Admin::create([
        'name' => '管理者',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);

    $user = User::factory()->create([
        'name' => '山田太郎',
    ]);

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'work_date' => '2026-05-28',
        'status' => 'finished',
        'clock_in' => '09:00:00',
        'clock_out' => '18:00:00',
    ]);

    BreakTime::create([
        'attendance_id' => $attendance->id,
        'break_start' => '12:00:00',
        'break_end' => '13:00:00',
    ]);

    $this->actingAs($admin, 'admin');

    $response = $this->get('/admin/attendance/' . $attendance->id);

    $response->assertStatus(200);

    $response->assertSee('山田太郎');


    $response->assertSee('2026年');
    $response->assertSee('5月28日');


    $response->assertSee('09:00');
    $response->assertSee('18:00');

    $response->assertSee('12:00');
    $response->assertSee('13:00');
}

    public function test_validation_message_is_displayed_when_clock_in_is_after_clock_out_by_admin(): void
{
    $admin = Admin::create([
        'name' => '管理者',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);

    $user = User::factory()->create();

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
        'clock_in' => '09:00:00',
        'clock_out' => '18:00:00',
    ]);

    $this->actingAs($admin, 'admin');

    $response = $this->from('/admin/attendance/' . $attendance->id)
        ->post('/admin/attendance/' . $attendance->id, [
            'request_clock_in' => '19:00',
            'request_clock_out' => '18:00',
            'reason' => 'テスト',
        ]);

    $response->assertRedirect('/admin/attendance/' . $attendance->id);

    $response->assertSessionHasErrors([
        'request_clock_out' => '出勤時間もしくは退勤時間が不適切な値です',
    ]);

    $this->actingAs($admin, 'admin')
        ->get('/admin/attendance/' . $attendance->id)
        ->assertSee('出勤時間もしくは退勤時間が不適切な値です');
}

    public function test_validation_message_is_displayed_when_break_start_is_after_clock_out_by_admin(): void
{
    $admin = Admin::create([
        'name' => '管理者',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);

    $user = User::factory()->create();

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
        'clock_in' => '09:00:00',
        'clock_out' => '18:00:00',
    ]);

    $this->actingAs($admin, 'admin');

    $response = $this->from('/admin/attendance/' . $attendance->id)
        ->post('/admin/attendance/' . $attendance->id, [
            'request_clock_in' => '09:00',
            'request_clock_out' => '18:00',
            'request_break_start' => ['19:00'],
            'request_break_end' => ['19:30'],
            'reason' => 'テスト',
        ]);

    $response->assertRedirect('/admin/attendance/' . $attendance->id);

    $response->assertSessionHasErrors([
        'request_break_start.0' => '休憩時間が不適切な値です',
    ]);

    $this->actingAs($admin, 'admin')
        ->get('/admin/attendance/' . $attendance->id)
        ->assertSee('休憩時間が不適切な値です');
}

    public function test_validation_message_is_displayed_when_break_end_is_after_clock_out_by_admin(): void
{
    $admin = Admin::create([
        'name' => '管理者',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);

    $user = User::factory()->create();

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
        'clock_in' => '09:00:00',
        'clock_out' => '18:00:00',
    ]);

    $this->actingAs($admin, 'admin');

    $response = $this->from('/admin/attendance/' . $attendance->id)
        ->post('/admin/attendance/' . $attendance->id, [
            'request_clock_in' => '09:00',
            'request_clock_out' => '18:00',
            'request_break_start' => ['17:00'],
            'request_break_end' => ['19:00'],
            'reason' => 'テスト',
        ]);

    $response->assertRedirect('/admin/attendance/' . $attendance->id);

    $response->assertSessionHasErrors([
        'request_break_end.0' => '休憩時間もしくは退勤時間が不適切な値です',
    ]);

    $this->actingAs($admin, 'admin')
        ->get('/admin/attendance/' . $attendance->id)
        ->assertSee('休憩時間もしくは退勤時間が不適切な値です');
}
 
    public function test_validation_message_is_displayed_when_reason_is_empty_by_admin(): void
{
    $admin = Admin::create([
        'name' => '管理者',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);

    $user = User::factory()->create();

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
        'clock_in' => '09:00:00',
        'clock_out' => '18:00:00',
    ]);

    $this->actingAs($admin, 'admin');

    $response = $this->from('/admin/attendance/' . $attendance->id)
        ->post('/admin/attendance/' . $attendance->id, [
            'request_clock_in' => '09:00',
            'request_clock_out' => '18:00',
            'request_break_start' => [],
            'request_break_end' => [],
            'reason' => '',
        ]);

    $response->assertRedirect('/admin/attendance/' . $attendance->id);

    $response->assertSessionHasErrors([
        'reason' => '備考を記入してください',
    ]);

    $this->actingAs($admin, 'admin')
        ->get('/admin/attendance/' . $attendance->id)
        ->assertSee('備考を記入してください');
}
}
