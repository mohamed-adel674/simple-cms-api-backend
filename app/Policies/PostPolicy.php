<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Post;
use Illuminate\Auth\Access\Response;

class PostPolicy
{
    /**
     * قبل أي تحقق، إذا كان المستخدم Admin، اسمح له بالوصول.
     * @param User $user
     * @param string $ability
     * @return bool|void
     */
    public function before(User $user, string $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }

    /**
     * تحديد إذا كان يمكن للمستخدم عرض أي مقالة. (للتحقق من تسجيل الدخول فقط).
     * @param User $user
     * @return bool
     */
    public function viewAny(?User $user): bool
    {
        // الجميع يمكنهم الوصول إلى قائمة المقالات (لكن الـ Controller يحدد ما يُعرض).
        return true;
    }

    /**
     * تحديد إذا كان يمكن للمستخدم عرض المقالة المحددة.
     * @param User $user
     * @param Post $post
     * @return bool
     */
    public function view(?User $user, Post $post): bool
    {
        // يمكن للجميع عرض المقالات المنشورة.
        if ($post->status === 'published') {
            return true;
        }
        
        // يمكن للمؤلف أو الـ Admin رؤية المسودة الخاصة به.
        return $user && $user->id === $post->user_id;
    }

    /**
     * تحديد إذا كان يمكن للمستخدم إنشاء مقالات.
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // يمكن فقط للمسجلين الذين لديهم دور Author أو Admin إنشاء مقالات.
        return $user->role === 'author' || $user->role === 'admin';
    }

    /**
     * تحديد إذا كان يمكن للمستخدم تحديث المقالة المحددة.
     * @param User $user
     * @param Post $post
     * @return bool
     */
    public function update(User $user, Post $post): bool
    {
        // يمكن فقط للمستخدم الذي أنشأ المقالة (المالك) تعديلها.
        return $user->id === $post->user_id;
    }

    /**
     * تحديد إذا كان يمكن للمستخدم حذف المقالة المحددة.
     * @param User $user
     * @param Post $post
     * @return bool
     */
    public function delete(User $user, Post $post): bool
    {
        // يمكن فقط للمالك حذف المقالة.
        return $user->id === $post->user_id;
    }
}