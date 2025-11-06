<?php

use Illuminate\Support\Facades\Route;

use App\Http\Controllers\AuthController;
use App\Http\Controllers\PostController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\CommentController;

// 1. مسارات المصادقة (مفتوحة)
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// 2. مسارات المقالات والتصنيفات (للعرض العام)
Route::get('/posts', [PostController::class, 'index']);
Route::get('/posts/{post:slug}', [PostController::class, 'show']); // استخدام slug
Route::resource('categories', CategoryController::class)->only(['index', 'show']);

// 3. مسارات التعليقات (لإضافة تعليق على مقال)
Route::post('/posts/{post}/comments', [CommentController::class, 'store']);


// 4. مسارات الإدارة (مؤمنة بـ Sanctum)
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);

    // إدارة المقالات (CRUD)
    Route::resource('posts', PostController::class)->except(['index', 'show']);

    // إدارة التصنيفات (مؤمنة بالكامل)
    Route::resource('categories', CategoryController::class)->except(['index', 'show']);
    
    // إدارة التعليقات (موافقة وحذف - للمدراء)
    Route::delete('/comments/{comment}', [CommentController::class, 'destroy']);
    Route::patch('/comments/{comment}/approve', [CommentController::class, 'approve']);
});