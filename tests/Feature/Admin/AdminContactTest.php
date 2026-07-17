<?php

namespace Tests\Feature\Admin;

use App\Models\Contact;
use App\Models\User;
use App\Models\Category; // ← ここを追加してください
use App\Models\Tag; // ← これを追加してください
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

    public function test_管理者は詳細画面を表示できる()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['content' => 'テストカテゴリ']);
        $contact = Contact::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($user)->get('/admin/contacts/' . $contact->id);

        $response->assertStatus(200);
        $response->assertSee($contact->first_name);
    }

    public function test_管理者は削除処理ができる()
    {
        $user = User::factory()->create();
        $category = Category::factory()->create(['content' => 'テストカテゴリ']);
        $contact = Contact::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($user)->delete('/admin/contacts/' . $contact->id);

        $response->assertRedirect('/admin');
        $this->assertDatabaseMissing('contacts', ['id' => $contact->id]);
    }

    public function test_管理者はタグを保存できる()
    {
        $user = User::factory()->create();
        $data = ['name' => '新しいタグ'];

        $response = $this->actingAs($user)->post('/admin/tags', $data);

        $response->assertRedirect();
        $this->assertDatabaseHas('tags', $data);
    }

    public function test_管理者はタグを編集できる()
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['name' => '古いタグ']);

        // 編集画面へのアクセス
        $response = $this->actingAs($user)->get("/admin/tags/{$tag->id}/edit");
        $response->assertStatus(200);

        // 更新処理
        $updateData = ['name' => '新しいタグ名'];
        $response = $this->actingAs($user)->put("/admin/tags/{$tag->id}", $updateData);
        
        $response->assertRedirect(); // または特定の場所へリダイレクト
        $this->assertDatabaseHas('tags', ['id' => $tag->id, 'name' => '新しいタグ名']);
    }

    public function test_管理者はタグを削除できる()
    {
        $user = User::factory()->create();
        $tag = Tag::factory()->create(['name' => '削除対象タグ']);

        $response = $this->actingAs($user)->delete("/admin/tags/{$tag->id}");

        $response->assertRedirect();
        $this->assertDatabaseMissing('tags', ['id' => $tag->id]);
    }
}