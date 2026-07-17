<?php

namespace Tests\Feature;

use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class ContactFormTest extends TestCase
{
    use RefreshDatabase;

    public function test_入力フォームで送信した内容が確認画面に正しく表示される()
    {
        // 事前にカテゴリを作成しておく（existsチェック対策）
        $category = Category::factory()->create([
            'content' => 'テストカテゴリ内容'
        ]);

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => 'テストビル',
            'category_id' => $category->id,
            'detail' => 'テストの内容です。',
        ];

        // POST /contacts/confirm に送信
        $response = $this->post('/contacts/confirm', $data);

        // 確認画面が表示されること
        $response->assertStatus(200);
        
        // 入力した内容が画面に含まれていること
        $response->assertSee('山田');
        $response->assertSee('太郎');
        $response->assertSee('test@example.com');
    }

    public function test_お問い合わせを送信すると保存されてサンクスページにリダイレクトされる()
    {
        $category = Category::factory()->create(['content' => 'テストカテゴリ']);

        $data = [
            'first_name' => '山田',
            'last_name' => '太郎',
            'gender' => 1,
            'email' => 'test@example.com',
            'tel' => '09012345678',
            'address' => '東京都渋谷区',
            'building' => 'テストビル',
            'category_id' => $category->id,
            'detail' => '送信テストの内容です。',
        ];

        // POST /contacts に送信
        $response = $this->post('/contacts', $data);

        // thanks にリダイレクトすること
        $response->assertRedirect('/thanks');

        // DBにデータが保存されていること
        $this->assertDatabaseHas('contacts', [
            'email' => 'test@example.com',
            'first_name' => '山田',
        ]);
    }
}