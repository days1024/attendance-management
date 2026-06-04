<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Admin;
use App\Models\Attendance;
use App\Models\AttendanceRequest;
class UserAttendanceEditTest extends TestCase
{
    use RefreshDatabase;
    public function test_validation_message_is_displayed_when_clock_in_is_after_clock_out(): void
{
    $user = User::factory()->create();

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
        'clock_in' => now()->setTime(9, 0),
        'clock_out' => now()->setTime(18, 0),
    ]);

    $this->actingAs($user);

    $response = $this->from('/attendance/detail/' . $attendance->id)
        ->post('/attendance/detail/' . $attendance->id, [
            'request_clock_in' => '19:00',
            'request_clock_out' => '18:00',
            'reason' => 'テスト',
        ]);

    $response->assertRedirect('/attendance/detail/' . $attendance->id);

    $response->assertSessionHasErrors([
    'request_clock_out' => '出勤時間もしくは退勤時間が不適切な値です',
]);
}

    public function test_validation_message_is_displayed_when_break_start_is_after_clock_out(): void
{
    $user = User::factory()->create();

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
        'clock_in' => now()->setTime(9, 0),
        'clock_out' => now()->setTime(18, 0),
    ]);

    $this->actingAs($user);

    $response = $this->from('/attendance/detail/' . $attendance->id)
        ->post('/attendance/detail/' . $attendance->id, [
            'request_clock_in' => '09:00',
            'request_clock_out' => '18:00',
            'request_break_start' => [
                '19:00', 
            ],
            'request_break_end' => [
                '19:30',
            ],
            'reason' => 'テスト',
        ]);

    $response->assertRedirect('/attendance/detail/' . $attendance->id);

    $response->assertSessionHasErrors([
    'request_break_start.0' => '休憩時間が不適切な値です',
]);
}    

    public function test_validation_message_is_displayed_when_break_end_is_after_clock_out(): void
{
    $user = User::factory()->create();

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
        'clock_in' => now()->setTime(9, 0),
        'clock_out' => now()->setTime(18, 0),
    ]);

    $this->actingAs($user);

    $response = $this->from('/attendance/detail/' . $attendance->id)
        ->post('/attendance/detail/' . $attendance->id, [
            'request_clock_in' => '09:00',
            'request_clock_out' => '18:00',
            'request_break_start' => [
                '12:00',
            ],
            'request_break_end' => [
                '19:00', 
            ],
            'reason' => 'テスト',
        ]);

    $response->assertRedirect('/attendance/detail/' . $attendance->id);

    $response->assertSessionHasErrors([
    'request_break_end.0' => '休憩時間もしくは退勤時間が不適切な値です',
    ]);
}

    public function test_validation_message_is_displayed_when_reason_is_empty(): void
{
    $user = User::factory()->create();

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
        'clock_in' => now()->setTime(9, 0),
        'clock_out' => now()->setTime(18, 0),
    ]);

    $this->actingAs($user);

    $response = $this->from('/attendance/detail/' . $attendance->id)
        ->post('/attendance/detail/' . $attendance->id, [
            'request_clock_in' => '09:00',
            'request_clock_out' => '18:00',
            'request_break_start' => [],
            'request_break_end' => [],
            'reason' => '',
        ]);

    $response->assertRedirect('/attendance/detail/' . $attendance->id);

    $response->assertSessionHasErrors([
        'reason' => '備考を記入してください',
    ]);
}

    public function test_attendance_edit_request_is_visible_in_admin_pages(): void
{

    $user = User::factory()->create();

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
        'clock_in' => now()->setTime(9, 0),
        'clock_out' => now()->setTime(18, 0),
    ]);

    $this->actingAs($user);

    $this->from('/attendance/detail/' . $attendance->id)
        ->post('/attendance/detail/' . $attendance->id, [
            'request_clock_in' => '09:00',
            'request_clock_out' => '18:00',
            'request_break_start' => [],
            'request_break_end' => [],
            'reason' => '時間修正のため',
        ]);

    $attendanceRequest = AttendanceRequest::where('attendance_id', $attendance->id)
    ->where('status', 'pending')
    ->first();

    $this->assertNotNull($attendanceRequest);

    $attendanceRequestId = $attendanceRequest->id;


    $this->assertDatabaseHas('attendance_requests', [
    'attendance_id' => $attendance->id,
    'status' => 'pending',
    'reason' => '時間修正のため',
    ]);

    

    $admin = Admin::create([
    'name' => '管理者',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    ]);

    $this->actingAs($admin, 'admin');

    $this->get('/stamp_correction_request/list')
    ->assertStatus(200)
    ->assertSee('時間修正のため');

    $this->get('/stamp_correction_request/approve/' . $attendanceRequestId)
    ->assertStatus(200);
}

    public function test_user_can_see_all_their_attendance_edit_requests(): void
{
    $user = User::factory()->create();

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
        'clock_in' => now()->setTime(9, 0),
        'clock_out' => now()->setTime(18, 0),
    ]);

    $this->actingAs($user);

    $this->from('/attendance/detail/' . $attendance->id)
        ->post('/attendance/detail/' . $attendance->id, [
            'request_clock_in' => '09:00',
            'request_clock_out' => '18:00',
            'request_break_start' => [],
            'request_break_end' => [],
            'reason' => '時間修正のため',
        ]);

    $this->assertDatabaseHas('attendance_requests', [
        'attendance_id' => $attendance->id,
        'status' => 'pending',
        'reason' => '時間修正のため',
    ]);

    $response = $this->get('/stamp_correction_request/list');

    $response->assertStatus(200);

    $response->assertSee('時間修正のため');
}

    public function test_user_can_navigate_to_attendance_request_detail_page(): void
{
    $user = User::factory()->create();

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
        'clock_in' => now()->setTime(9, 0),
        'clock_out' => now()->setTime(18, 0),
    ]);

    $this->actingAs($user);

    $this->post('/attendance/detail/' . $attendance->id, [
        'request_clock_in' => '09:00',
        'request_clock_out' => '18:00',
        'request_break_start' => [],
        'request_break_end' => [],
        'reason' => '時間修正のため',
    ]);

    $attendanceRequest = AttendanceRequest::where('attendance_id', $attendance->id)
        ->first();

    $this->assertNotNull($attendanceRequest);

    $response = $this->get('/stamp_correction_request/list');

    $response->assertStatus(200);
    $response->assertSee('詳細');

    $admin = Admin::create([
    'name' => '管理者',
    'email' => 'admin@example.com',
    'password' => bcrypt('password'),
    ]);

    $this->actingAs($admin, 'admin');

    $detailResponse = $this->get(
    '/stamp_correction_request/approve/' . $attendanceRequest->id
    );

    $detailResponse->assertStatus(200);

    $detailResponse->assertSee('時間修正のため');
}
}