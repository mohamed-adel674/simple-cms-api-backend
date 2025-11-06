<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
class AuthServiceProvider extends ServiceProvider
{

// ...
protected $policies = [
    Post::class => PostPolicy::class, // ⭐ أضف هذا السطر

    \App\Models\Category::class => \App\Policies\CategoryPolicy::class,
    \App\Models\Comment::class => \App\Policies\CommentPolicy::class,
];


}