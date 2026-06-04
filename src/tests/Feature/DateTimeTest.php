<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class DateTimeTest extends TestCase
{
    use RefreshDatabase;
    public function test_current_date_time_is_displayed(): void
{
    $user = User::factory()->create();
    $response = $this->actingAs($user)->get('/attendance');

    $response->assertStatus(200);

    $week = ['日', '月', '火', '水', '木', '金', '土'];

    $date = now()->format('Y年m月d日')
        . '(' . $week[now()->dayOfWeek] . ')';

    $response->assertSee($date);

    $response->assertSee(now()->format('H:i'));
}
}
