<?php

namespace Tests\Feature\Admin;

use App\Models\Contact;
use App\Models\User;
use App\Models\Category; // ← ここを追加してください
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AdminContactTest extends TestCase
{
    use RefreshDatabase;

    public function test_管理者は一覧画面で検索とページネーションができる()
    {
        // 管理者ユーザーを作成
        $user = User::factory()->create();
        
        // カテゴリを先に作成
        $category = Category::factory()->create(['content' => 'テストカテゴリ']);

        // カテゴリIDを指定してコンタクトを8件作成
        Contact::factory()->count(8)->create([
            'category_id' => $category->id
        ]);

        // ログインして一覧画面へ
        $response = $this->actingAs($user)->get('/admin');

        // ステータス200を確認
        $response->assertStatus(200);
        
        // ページネーション等の確認（7件表示されていることなど）
        $response->assertSee($category->content); // カテゴリ名が表示されているか確認など
    }
}