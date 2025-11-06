<?php

namespace App\Policies;

use App\Models\User;
use App\Models\Category;

class CategoryPolicy
{
    /**
     * تحديد إذا كان يمكن للمستخدم عرض قائمة التصنيفات.
     * @param User|null $user
     * @return bool
     */
    public function viewAny(?User $user): bool
    {
        // الجميع يمكنهم عرض قائمة التصنيفات.
        return true;
    }

    /**
     * تحديد إذا كان يمكن للمستخدم عرض تصنيف محدد.
     * @param User|null $user
     * @param Category $category
     * @return bool
     */
    public function view(?User $user, Category $category): bool
    {
        // الجميع يمكنهم عرض تفاصيل تصنيف.
        return true;
    }

    /**
     * تحديد إذا كان يمكن للمستخدم إنشاء تصنيفات.
     * @param User $user
     * @return bool
     */
    public function create(User $user): bool
    {
        // يمكن فقط للمستخدمين ذوي دور "admin" إنشاء تصنيفات.
        return $user->role === 'admin';
    }

    /**
     * تحديد إذا كان يمكن للمستخدم تحديث التصنيف.
     * @param User $user
     * @param Category $category
     * @return bool
     */
    public function update(User $user, Category $category): bool
    {
        // يمكن فقط للمستخدمين ذوي دور "admin" تعديل التصنيفات.
        return $user->role === 'admin';
    }

    /**
     * تحديد إذا كان يمكن للمستخدم حذف التصنيف.
     * @param User $user
     * @param Category $category
     * @return bool
     */
    public function delete(User $user, Category $category): bool
    {
        // يمكن فقط للمستخدمين ذوي دور "admin" حذف التصنيفات.
        return $user->role === 'admin';
    }
}