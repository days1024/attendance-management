<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Admin;
use App\Models\AttendanceRequest;
use App\Models\RequestBreakTime;
use App\Models\BreakTime;

class AdminAttendanceUpdateTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_can_see_all_pending_attendance_requests(): void
{
    $admin = Admin::create([
        'name' => '管理者',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);

    $user1 = User::factory()->create([
        'name' => '山田太郎',
    ]);

    $user2 = User::factory()->create([
        'name' => '佐藤花子',
    ]);

    $attendance1 = Attendance::create([
        'user_id' => $user1->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
        'clock_in' => '09:00:00',
        'clock_out' => '18:00:00',
    ]);

    $attendance2 = Attendance::create([
        'user_id' => $user2->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
        'clock_in' => '10:00:00',
        'clock_out' => '19:00:00',
    ]);

    AttendanceRequest::create([
        'attendance_id' => $attendance1->id,
        'request_clock_in' => '09:30:00',
        'request_clock_out' => '18:30:00',
        'reason' => '電車遅延',
        'status' => 'pending',
    ]);

    AttendanceRequest::create([
        'attendance_id' => $attendance2->id,
        'request_clock_in' => '10:30:00',
        'request_clock_out' => '19:30:00',
        'reason' => '業務対応',
        'status' => 'pending',
    ]);

    $this->actingAs($admin, 'admin');

    $response = $this->get('/stamp_correction_request/list?tab=pending');

    $response->assertStatus(200);

    $response->assertSee('山田太郎');
    $response->assertSee('佐藤花子');

    $response->assertSee('電車遅延');
    $response->assertSee('業務対応');

    $response->assertSee('承認待ち');
}

    public function test_admin_can_see_all_approved_attendance_requests(): void
{
    $admin = Admin::create([
        'name' => '管理者',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);

    $user1 = User::factory()->create([
        'name' => '山田太郎',
    ]);

    $user2 = User::factory()->create([
        'name' => '佐藤花子',
    ]);

    $attendance1 = Attendance::create([
        'user_id' => $user1->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
        'clock_in' => '09:00:00',
        'clock_out' => '18:00:00',
    ]);

    $attendance2 = Attendance::create([
        'user_id' => $user2->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
        'clock_in' => '10:00:00',
        'clock_out' => '19:00:00',
    ]);

    AttendanceRequest::create([
        'attendance_id' => $attendance1->id,
        'request_clock_in' => '09:30:00',
        'request_clock_out' => '18:30:00',
        'reason' => '電車遅延',
        'status' => 'approved',
    ]);

    AttendanceRequest::create([
        'attendance_id' => $attendance2->id,
        'request_clock_in' => '10:30:00',
        'request_clock_out' => '19:30:00',
        'reason' => '業務対応',
        'status' => 'approved',
    ]);

    $this->actingAs($admin, 'admin');

    $response = $this->get('/stamp_correction_request/list?tab=approved');

    $response->assertStatus(200);

    $response->assertSee('山田太郎');
    $response->assertSee('佐藤花子');

    $response->assertSee('電車遅延');
    $response->assertSee('業務対応');

    $response->assertSee('承認済み');
}

    public function test_admin_can_approve_attendance_request(): void
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

    $attendanceRequest = AttendanceRequest::create([
        'attendance_id' => $attendance->id,
        'request_clock_in' => '10:00:00',
        'request_clock_out' => '19:00:00',
        'reason' => '電車遅延のため',
        'status' => 'pending',
    ]);

    RequestBreakTime::create([
        'attendance_request_id' => $attendanceRequest->id,
        'request_break_start' => '12:00:00',
        'request_break_end' => '13:00:00',
    ]);

    $this->actingAs($admin, 'admin');

    $response = $this->post(
        '/stamp_correction_request/approve/' . $attendanceRequest->id
    );

    $response->assertStatus(302);

    $this->assertDatabaseHas('attendance_requests', [
        'id' => $attendanceRequest->id,
        'status' => 'approved',
    ]);

    $this->assertDatabaseHas('attendances', [
        'id' => $attendance->id,
        'clock_in' => '2026-05-28 10:00:00',
        'clock_out' => '2026-05-28 19:00:00',
    ]);

    $this->assertDatabaseHas('break_times', [
        'attendance_id' => $attendance->id,
        'break_start' => '2026-05-28 12:00:00',
        'break_end' => '2026-05-28 13:00:00',
    ]);
}
}
