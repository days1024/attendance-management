<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use App\Models\Attendance;

class UserAttendanceIndexTest extends TestCase
{
    use RefreshDatabase;
    public function test_all_attendance_records_are_displayed(): void
{
    $user = User::factory()->create();

    $attendance1 = Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->subDays(1)->toDateString(),
        'status' => 'finished',
        'clock_in' => now()->subDays(1)->setTime(9, 0),
        'clock_out' => now()->subDays(1)->setTime(18, 0),
    ]);

    $attendance2 = Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
        'clock_in' => now()->setTime(10, 0),
        'clock_out' => now()->setTime(19, 0),
    ]);

    $response = $this->actingAs($user)->get('/attendance/list');

    $response->assertStatus(200);

    $week = ['日','月','火','水','木','金','土'];

    $date1 = $attendance1->clock_in->format('m/d')
        . '(' . $week[$attendance1->clock_in->dayOfWeek] . ')';

    $date2 = $attendance2->clock_in->format('m/d')
        . '(' . $week[$attendance2->clock_in->dayOfWeek] . ')';

    $response->assertSee($date1);
    $response->assertSee('09:00');
    $response->assertSee('18:00');

    $response->assertSee($date2);
    $response->assertSee('10:00');
    $response->assertSee('19:00');
}

    public function test_current_month_is_displayed(): void
{
    $user = User::factory()->create();

    $response = $this->actingAs($user)->get('/attendance/list');

    $response->assertStatus(200);

    $response->assertSee(now()->format('Y/m'));
}

    public function test_previous_month_attendance_is_displayed(): void
{
    $user = User::factory()->create();

    $previousMonthDate = now()->subMonth();

    Attendance::create([
        'user_id' => $user->id,
        'work_date' => $previousMonthDate->toDateString(),
        'status' => 'finished',
        'clock_in' => $previousMonthDate->copy()->setTime(9, 0),
        'clock_out' => $previousMonthDate->copy()->setTime(18, 0),
    ]);

    $response = $this->actingAs($user)->get(
        '/attendance/list?month=' . $previousMonthDate->format('Y-m-d')
    );

    $response->assertStatus(200);

    $response->assertSee($previousMonthDate->format('Y/m'));

    $week = ['日','月','火','水','木','金','土'];

    $date = $previousMonthDate->format('m/d')
        . '(' . $week[$previousMonthDate->dayOfWeek] . ')';

    $response->assertSee($date);

    $response->assertSee('09:00');
    $response->assertSee('18:00');
}

    public function test_next_month_attendance_is_displayed(): void
{
    $user = User::factory()->create();

    $nextMonthDate = now()->addMonth();

    Attendance::create([
        'user_id' => $user->id,
        'work_date' => $nextMonthDate->toDateString(),
        'status' => 'finished',
        'clock_in' => $nextMonthDate->copy()->setTime(9, 0),
        'clock_out' => $nextMonthDate->copy()->setTime(18, 0),
    ]);

    $response = $this->actingAs($user)->get(
        '/attendance/list?month=' . $nextMonthDate->format('Y-m-d')
    );

    $response->assertStatus(200);

    $response->assertSee($nextMonthDate->format('Y/m'));

    $week = ['日','月','火','水','木','金','土'];

    $date = $nextMonthDate->format('m/d')
        . '(' . $week[$nextMonthDate->dayOfWeek] . ')';

    $response->assertSee($date);

    $response->assertSee('09:00');
    $response->assertSee('18:00');
}

    public function test_attendance_detail_page_is_displayed(): void
{
    $user = User::factory()->create();

    $attendance = Attendance::create([
        'user_id' => $user->id,
        'work_date' => now()->toDateString(),
        'status' => 'finished',
    ]);

    $response = $this->actingAs($user)->get(
        '/attendance/detail/' . $attendance->id
    );

    $response->assertStatus(200);
}
}
