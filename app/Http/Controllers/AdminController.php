<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\Category;
use App\Models\Tag; // 追加
use App\Http\Requests\IndexContactRequest; // 追加
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function index(IndexContactRequest $request)
    {
        // クエリビルダーの開始
        $query = Contact::query();

        // 名前（姓・名を検索対象にする）
        if ($request->filled('name')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->name . '%')
                  ->orWhere('last_name', 'like', '%' . $request->name . '%');
            });
        }

        // 性別
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }

        // カテゴリ
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        // 日付（created_at を日付単位で比較）
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // ページネーション（検索条件を保持）
        $contacts = $query->with(['category'])->latest()->paginate(7)->withQueryString();
    
        $categories = Category::all();
        $tags = Tag::all();

        return view('admin.index', compact('contacts', 'categories', 'tags'));
    }
    public function show($id)
    {
        // リレーション（category, tags）を含めて取得
        $contact = Contact::with(['category', 'tags'])->findOrFail($id);
    
        return view('admin.show', compact('contact'));
    }
}