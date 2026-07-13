<?php

use App\Http\Controllers\ContactController;
use App\Http\Controllers\AdminController; // 必要に応じて
use Illuminate\Support\Facades\Route;

// 1. お問い合わせフォーム関連（公開画面）
Route::get('/', [ContactController::class, 'index']);           // PG01: 入力ページ
Route::post('/contacts/confirm', [ContactController::class, 'confirm']); // PG02: 確認ページ
Route::post('/contacts', [ContactController::class, 'store']); // 保存処理
Route::get('/thanks', [ContactController::class, 'thanks']);    // PG03: サンクスページ

// 2. 認証が必要なルート（管理画面など）
Route::middleware(['auth'])->group(function () {
    // ログイン後のリダイレクト先を /contacts にしているため
    Route::get('/contacts', function () {
        return 'お問い合わせ一覧画面です';
    });
    
    // 今後ここに管理画面のルートを追加していきます
    // Route::get('/admin', [AdminController::class, 'index']);
});