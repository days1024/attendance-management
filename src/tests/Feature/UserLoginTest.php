<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;

class UserLoginTest extends TestCase
{
    use RefreshDatabase;
    public function test_email_is_required_for_login(): void
{
    $response = $this->post('/login', [
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors([
        'email' => 'メールアドレスを入力してください',
    ]);
}
    
    public function test_password_is_required_for_login(): void
{
    $response = $this->post('/login', [
        'email' => 'test@example.com',
    ]);

    $response->assertSessionHasErrors([
        'password' => 'パスワードを入力してください',
    ]);
}

    public function test_login_fails_with_invalid_email(): void
{
    User::factory()->create([
        'email' => 'test@example.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->post('/login', [
        'email' => 'wrong@example.com',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors([
        'email' => 'ログイン情報が登録されていません',
    ]);
}
}
