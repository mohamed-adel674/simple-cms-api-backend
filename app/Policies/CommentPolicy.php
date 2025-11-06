<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Comment;

class CommentPolicy
{
    /**
     * قبل أي تحقق، إذا كان المستخدم Admin، اسمح له بالوصول.
     */
    public function before(User $user, string $ability)
    {
        if ($user->role === 'admin') {
            return true;
        }
    }

    /**
     * تحديد إذا كان يمكن للمستخدم حذف التعليق المحدد.
     */
    public function delete(User $user, Comment $comment): bool
    {
        // بما أننا استخدمنا 'before' hook، هذا الجزء يضمن أن غير المدراء لا يمكنهم الحذف.
        // يمكننا إضافة شرط آخر: هل هو مالك التعليق؟ (لكن نفضل أن تكون الإدارة مركزة بيد Admin)
        return $user->role === 'admin';
    }

    /**
     * تحديد إذا كان يمكن للمستخدم الموافقة على التعليق (دالة مخصصة).
     */
    public function approve(User $user, Comment $comment): bool
    {
        // فقط المدراء يمكنهم الموافقة.
        return $user->role === 'admin';
    }
}