<?php

namespace Tests\Feature\Admin;

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class DashboardTest extends TestCase
{
    use RefreshDatabase;

    public function test_未ログインユーザーはログイン画面にリダイレクトされる()
    {
        $response = $this->get('/admin');
        $response->assertRedirect('/login');
    }

    public function test_ログインユーザーはダッシュボードにアクセスできる()
    {
        $user = User::factory()->create();
        
        $response = $this->actingAs($user)->get('/admin');
        $response->assertStatus(200);
    }
}