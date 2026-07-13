<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Tag;
use Illuminate\Http\Request;
use App\Http\Requests\StoreContactRequest; // 先ほど作成したリクエストクラス

class ContactController extends Controller
{
    public function index()
    {
        // データベースからカテゴリとタグを取得
        $categories = Category::all();
        $tags = Tag::all();

        // viewへ変数を渡す
        return view('contact.index', compact('categories', 'tags'));
    }

    public function confirm(\App\Http\Requests\StoreContactRequest $request)
    {
        // $request->all() を $validated という変数名で受け取る
        $validated = $request->all();
        
        // カテゴリ名をIDから取得
        $category = \App\Models\Category::find($validated['category_id']);
        
        // 変数名を 'validated' と 'category' に合わせる
        return view('contact.confirm', compact('validated', 'category'));
    }

    public function store(\App\Http\Requests\StoreContactRequest $request)
    {
        // 必要な項目だけを抽出して保存
        $data = $request->only([
            'first_name', 'last_name', 'gender', 'email', 'tel', 
            'address', 'building', 'category_id', 'detail'
        ]);

        // contactsテーブルにデータを保存
        $contact = \App\Models\Contact::create($data);

        // タグが選択されている場合、中間テーブル(contact_tag)にデータを保存
        if ($request->has('tag_ids')) {
            $contact->tags()->sync($request->tag_ids);
        }

        // サンクスページへリダイレクト
        return redirect('/thanks');
    }

    public function thanks()
    {
        return view('contact.thanks');
    }
}