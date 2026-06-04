<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Illuminate\Support\Facades\Notification;
use Illuminate\Auth\Notifications\VerifyEmail;
use Illuminate\Support\Facades\URL;

class EmailVerificationTest extends TestCase
{
    use RefreshDatabase;
    public function test_verification_email_is_sent_after_registration(): void
{
    Notification::fake();

    $response = $this->post('/register', [
        'name' => '山田太郎',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertStatus(302);

    $user = User::where('email', 'test@example.com')->first();

    $this->assertNotNull($user);

    Notification::assertSentTo(
        $user,
        VerifyEmail::class
    );
}

    public function test_user_can_access_email_verification_page(): void
{
    Notification::fake();

    $user = User::factory()->unverified()->create([
        'email' => 'test@example.com',
    ]);

    $this->actingAs($user);

    $response = $this->get('/email/verify');

    $response->assertStatus(200);

    $verificationUrl = URL::temporarySignedRoute(
        'verification.verify',
        now()->addMinutes(60),
        [
            'id' => $user->id,
            'hash' => sha1($user->email),
        ]
    );

    $verifyResponse = $this->get($verificationUrl);

    $verifyResponse->assertRedirect('/attendance?verified=1');

    $this->assertTrue($user->fresh()->hasVerifiedEmail());
}

    public function test_verified_user_can_access_attendance_page(): void
{
    $user = User::factory()->create([
        'email_verified_at' => now(),
    ]);

    $this->actingAs($user);

    $response = $this->get('/attendance');

    $response->assertStatus(200);
}
}
