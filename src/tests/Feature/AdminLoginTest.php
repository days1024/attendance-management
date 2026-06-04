<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\Admin;


class AdminLoginTest extends TestCase
{
    use RefreshDatabase;
    public function test_email_is_required_for_admin_login(): void
{
    Admin::factory()->create([
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->post('/admin/login', [
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors([
        'email' => 'メールアドレスを入力してください',
    ]);
}

    public function test_password_is_required_for_admin_login(): void
{
    Admin::factory()->create([
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->post('/admin/login', [
        'email' => 'admin@example.com',
    ]);

    $response->assertSessionHasErrors([
        'password' => 'パスワードを入力してください',
    ]);
}

    public function test_admin_login_fails_with_invalid_email(): void
{
    Admin::factory()->create([
        'email' => 'admin@example.com',
        'password' => bcrypt('password'),
    ]);

    $response = $this->post('/admin/login', [
        'email' => 'wrong@example.com',
        'password' => 'password',
    ]);

    $response->assertSessionHasErrors([
        'email' => 'ログイン情報が登録されていません',
    ]);
}
}
