<?php

namespace App\Http\Controllers;

use App\Models\Tag;
use App\Http\Requests\TagRequest; // 先ほど作ったRequestをインポート

class TagController extends Controller
{
    public function store(TagRequest $request)
    {
        // バリデーション済みのデータでタグを作成
        Tag::create($request->only('name'));

        // 管理画面へリダイレクト（成功メッセージ付き）
        return redirect('/admin')->with('success', 'タグを追加しました');
    }

    public function destroy($id)
    {
        // タグを検索して削除
        $tag = Tag::find($id);
    
    // タグが見つかれば削除処理を実行
    if ($tag) {
        $tag->delete();
    }

    // 管理画面へリダイレクト
    return redirect('/admin')->with('success', 'タグを削除しました');
    }

    public function edit($id)
    {
        // 編集対象のタグを取得
        $tag = Tag::findOrFail($id);
    
        // 編集画面を表示（ディレクトリを作成してそこに配置する想定です）
        return view('admin.tags.edit', compact('tag'));
    }

    public function update(TagRequest $request, $id)
    {
        // タグを検索（見つからなければ404）
        $tag = Tag::findOrFail($id);
    
        // バリデーション済みのデータで更新
        $tag->update($request->only('name'));
    
        // 管理画面へリダイレクト
        return redirect('/admin')->with('success', 'タグを更新しました');
    }
}