<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Http\Requests\Api\V1\IndexContactRequest;
use App\Http\Resources\ContactResource;
use App\Models\Contact;
use App\Http\Requests\Api\V1\StoreContactRequest;
use Illuminate\Http\Request;

class ContactController extends Controller
{
    public function index(IndexContactRequest $request)
    {
        $query = Contact::query();

        // 検索条件
        if ($request->filled('keyword')) {
            $query->where(function ($q) use ($request) {
                $q->where('first_name', 'like', '%' . $request->keyword . '%')
                  ->orWhere('last_name', 'like', '%' . $request->keyword . '%')
                  ->orWhere('email', 'like', '%' . $request->keyword . '%');
            });
        }
        if ($request->filled('gender')) {
            $query->where('gender', $request->gender);
        }
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        if ($request->filled('date')) {
            $query->whereDate('created_at', $request->date);
        }

        // ページネーション（per_page対応）
        $perPage = $request->input('per_page', 20);
        $contacts = $query->with(['category', 'tags'])->latest()->paginate($perPage);

        return ContactResource::collection($contacts);
    }

    public function show($id)
    {
        // 指定IDのお問い合わせ詳細を取得し、リソースでラップする
        $contact = Contact::with(['category', 'tags'])->findOrFail($id);
    
        return new ContactResource($contact);
    }

    public function store(StoreContactRequest $request)
    {
        // データを保存
        $contact = Contact::create($request->validated());

        // タグが送信されていれば紐付け
        if ($request->has('tag_ids')) {
            $contact->tags()->attach($request->tag_ids);
        }

        // リレーションをロードしてResourceで返す
        $contact->load(['category', 'tags']);
    
        return (new ContactResource($contact))
                    ->response()
                    ->setStatusCode(201);
    }

    public function update(StoreContactRequest $request, $id)
    {
        // 指定IDのお問い合わせを取得
        $contact = Contact::findOrFail($id);

        // データを更新
        $contact->update($request->validated());

        // タグを同期（syncを使うことで、古い紐付けを削除し新しいものに置き換えます）
        if ($request->has('tag_ids')) {
            $contact->tags()->sync($request->tag_ids);
        } else {
            $contact->tags()->detach();
        }

        // 更新後のデータをロードして返却
        $contact->load(['category', 'tags']);
    
        return new ContactResource($contact);
    }

    public function destroy($id)
    {
        // 指定IDのお問い合わせを取得
        $contact = Contact::findOrFail($id);
    
        // 削除を実行
        // contact_tag テーブルの関連レコードは外部キー制約の cascade により自動削除されます
        $contact->delete();
    
        // 204 No Content を返却
        return response()->json(null, 204);
    }
}
