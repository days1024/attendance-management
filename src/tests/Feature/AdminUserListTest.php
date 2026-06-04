<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;
use App\Models\Admin;

class AdminUserListTest extends TestCase
{
     use RefreshDatabase;
     public function test_admin_can_see_all_users_name_and_email(): void
{
    $admin = Admin::create([
        'name' => '管理者',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);

    $user1 = User::factory()->create([
        'name' => '山田太郎',
        'email' => 'yamada@example.com',
    ]);

    $user2 = User::factory()->create([
        'name' => '佐藤花子',
        'email' => 'sato@example.com',
    ]);

    $this->actingAs($admin, 'admin');

    $response = $this->get('/admin/staff/list');

    $response->assertStatus(200);

    $response->assertSee('山田太郎');
    $response->assertSee('佐藤花子');

    $response->assertSee('yamada@example.com');
    $response->assertSee('sato@example.com');
}

    public function test_admin_can_view_previous_month_attendance(): void
{
    $admin = Admin::create([
        'name' => '管理者',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);

    $user = User::factory()->create([
        'name' => '山田太郎',
    ]);

    $previousMonthDate = now()->subMonth()->startOfMonth()->toDateString();

    Attendance::create([
        'user_id' => $user->id,
        'work_date' => $previousMonthDate,
        'status' => 'finished',
        'clock_in' => '09:00:00',
        'clock_out' => '18:00:00',
    ]);

    $this->actingAs($admin, 'admin');

    $response = $this->get(
        '/admin/attendance/staff/' . $user->id .
        '?month=' . \Carbon\Carbon::parse($previousMonthDate)->format('Y-m')
    );

    $response->assertStatus(200);

    $response->assertSee(
        \Carbon\Carbon::parse($previousMonthDate)->format('Y/m')
    );

    $response->assertSee('山田太郎');
    $response->assertSee('09:00');
    $response->assertSee('18:00');
}

    public function test_admin_can_view_next_month_attendance(): void
{
    $admin = Admin::create([
        'name' => '管理者',
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);

    $user = User::factory()->create([
        'name' => '山田太郎',
    ]);

    $nextMonthDate = now()->addMonth()->startOfMonth()->toDateString();

    Attendance::create([
        'user_id' => $user->id,
        'work_date' => $nextMonthDate,
        'status' => 'finished',
        'clock_in' => '09:00:00',
        'clock_out' => '18:00:00',
    ]);

    $this->actingAs($admin, 'admin');

    $response = $this->get(
        '/admin/attendance/staff/' . $user->id .
        '?month=' . \Carbon\Carbon::parse($nextMonthDate)->format('Y-m')
    );

    $response->assertStatus(200);

    $response->assertSee(
        \Carbon\Carbon::parse($nextMonthDate)->format('Y/m')
    );

    $response->assertSee('山田太郎');
    $response->assertSee('09:00');
    $response->assertSee('18:00');
}

    public function test_admin_can_navigate_to_attendance_detail_page(): void
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
        'work_date' => now()->toDateString(),
        'status' => 'finished',
        'clock_in' => '09:00:00',
        'clock_out' => '18:00:00',
    ]);

    $this->actingAs($admin, 'admin');

    $response = $this->get('/admin/attendance/list');

    $response->assertStatus(200);

    $response->assertSee('詳細');

    $detailResponse = $this->get(
        '/admin/attendance/' . $attendance->id
    );

    $detailResponse->assertStatus(200);

    $detailResponse->assertSee('山田太郎');
    $detailResponse->assertSee('09:00');
    $detailResponse->assertSee('18:00');
}
}
