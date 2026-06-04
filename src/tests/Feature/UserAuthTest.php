<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class UserAuthTest extends TestCase
{

    use RefreshDatabase;
    public function test_name_is_required_for_register(): void
{
    $response = $this->post('/register', [
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors([
        'name' => 'お名前を入力してください',
    ]);
}

    public function test_email_is_required_for_register(): void
{
    $response = $this->post('/register', [
        'name' => 'テストユーザー',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors([
        'email' => 'メールアドレスを入力してください',
    ]);
}

    public function test_password_must_be_at_least_8_characters(): void
{
    $response = $this->post('/register', [
        'name' => 'テストユーザー',
        'email' => 'test@example.com',
        'password' => '1234567',
        'password_confirmation' => '1234567',
    ]);

    $response->assertSessionHasErrors([
        'password' => 'パスワードは8文字以上で入力してください',
    ]);
}

    public function test_password_confirmation_does_not_match(): void
{
    $response = $this->post('/register', [
        'name' => 'テストユーザー',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'different_password',
    ]);

    $response->assertSessionHasErrors([
        'password' => 'パスワードと一致しません',
    ]);
}

    
    public function test_password_is_required_for_register(): void
{
    $response = $this->post('/register', [
        'name' => 'テストユーザー',
        'email' => 'test@example.com',
        'password_confirmation' => 'password',
    ]);

    $response->assertSessionHasErrors([
        'password' => 'パスワードを入力してください',
    ]);
}

    public function test_user_can_register(): void
{
    $this->post('/register', [
        'name' => 'テストユーザー',
        'email' => 'test@example.com',
        'password' => 'password',
        'password_confirmation' => 'password',
    ]);

    $this->assertDatabaseHas('users', [
        'name' => 'テストユーザー',
        'email' => 'test@example.com',
    ]);
}
}
