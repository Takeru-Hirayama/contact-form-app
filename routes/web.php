<?php

use Illuminate\Support\Facades\Route;

// ログインが必要なルートとして定義
Route::middleware(['auth'])->group(function () {
    Route::get('/contacts', function () {
        return 'お問い合わせ一覧画面です'; // 一旦テキストを表示
    });
});

Route::get('/', function () {
    return view('welcome');
});
