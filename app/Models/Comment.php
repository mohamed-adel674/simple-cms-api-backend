<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Comment extends Model
{
    use HasFactory;

    protected $fillable = [
        'post_id',
        'user_id',
        'content',
        'approved', // ⭐ مهم للسماح بتعيين حالة الموافقة
    ];

    /**
     * العلاقة: التعليق ينتمي إلى مقالة.
     */
    public function post()
    {
        return $this->belongsTo(Post::class);
    }

    /**
     * العلاقة: التعليق ينتمي إلى مستخدم (يمكن أن يكون null للضيوف).
     */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}