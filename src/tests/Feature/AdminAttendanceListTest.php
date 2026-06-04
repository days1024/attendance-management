<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Admin;

class AdminAttendanceListTest extends TestCase
{
    use RefreshDatabase;
    public function test_admin_can_see_all_users_attendance_list(): void
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

    Attendance::create([
        'user_id' => $user1->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
        'clock_in' => '09:00:00',
        'clock_out' => '18:00:00',
    ]);

    Attendance::create([
        'user_id' => $user2->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
        'clock_in' => '10:00:00',
        'clock_out' => '19:00:00',
    ]);

    $this->actingAs($admin, 'admin');

    $response = $this->get('/admin/attendance/list');

    $response->assertStatus(200);

    $response->assertSee('山田太郎');
    $response->assertSee('佐藤花子');

    $response->assertSee('09:00');
    $response->assertSee('10:00');

    $response->assertSee('18:00');
    $response->assertSee('19:00');
}

    public function test_today_date_is_displayed_on_admin_attendance_list_page(): void
{
    $admin = Admin::create([
        'name' => '管理者',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);

    $this->actingAs($admin, 'admin');

    $today = now()->format('Y/m/d');

    $response = $this->get('/admin/attendance/list');

    $response->assertStatus(200);

    $response->assertSee($today);
}

    public function test_admin_can_view_previous_day_attendance_list(): void
{
    $admin = Admin::create([
        'name' => '管理者',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);

    $user = User::factory()->create([
        'name' => '山田太郎',
    ]);

    $previousDate = now()->subDay()->toDateString();

    Attendance::create([
        'user_id' => $user->id,
        'work_date' => $previousDate,
        'status' => 'finished',
        'clock_in' => '09:00:00',
        'clock_out' => '18:00:00',
    ]);

    $this->actingAs($admin, 'admin');

    $response = $this->get('/admin/attendance/list?day=' . $previousDate);

    $response->assertStatus(200);

    $response->assertSee(
    \Carbon\Carbon::parse($previousDate)->format('Y/m/d')
    );

    $response->assertSee('山田太郎');
    $response->assertSee('09:00');
    $response->assertSee('18:00');
}

    public function test_admin_can_view_next_day_attendance_list(): void
{

    $admin = Admin::create([
        'name' => '管理者',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);

    $user = User::factory()->create([
        'name' => '山田太郎',
    ]);

    $nextDate = now()->addDay()->toDateString();

    Attendance::create([
        'user_id' => $user->id,
        'work_date' => $nextDate,
        'status' => 'finished',
        'clock_in' => '09:00:00',
        'clock_out' => '18:00:00',
    ]);

    $this->actingAs($admin, 'admin');

    $response = $this->get('/admin/attendance/list?day=' . $nextDate);

    $response->assertStatus(200);

    $response->assertSee(
        \Carbon\Carbon::parse($nextDate)->format('Y/m/d')
    );

    $response->assertSee('山田太郎');
    $response->assertSee('09:00');
    $response->assertSee('18:00');
}
}
