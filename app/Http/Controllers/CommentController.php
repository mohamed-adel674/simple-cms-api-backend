<?php

namespace App\Http\Controllers;

use App\Models\Post;
use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CommentController extends Controller
{
    /**
     * إضافة تعليق جديد على مقالة محددة. (متاح للجميع)
     */
    public function store(Request $request, Post $post)
    {
        // 1. التحقق من صحة البيانات
        $fields = $request->validate([
            'content' => 'required|string|max:1000',
            // يمكن إضافة 'guest_name' إذا كان المستخدم غير مسجل.
        ]);

        // 2. إنشاء التعليق
        $comment = $post->comments()->create([
            'content' => $fields['content'],
            'user_id' => Auth::id(), // سيكون null إذا كان ضيفاً
            'approved' => false, // ⭐ التعليق يحتاج دائماً إلى موافقة مبدئية
        ]);

        return response()->json([
            'message' => 'تم استلام التعليق، وسينشر بعد موافقة المدير.',
            'comment' => $comment
        ], 201);
    }

    /**
     * الموافقة على تعليق (للمدير فقط).
     */
    public function approve(Comment $comment)
    {
        // 1. التحقق من صلاحية الموافقة (Policy Check: Admin only)
        $this->authorize('approve', $comment);

        // 2. تحديث حالة الموافقة
        $comment->approved = true;
        $comment->save();

        return response()->json([
            'message' => 'تمت الموافقة على التعليق بنجاح.',
            'comment' => $comment
        ], 200);
    }

    /**
     * حذف تعليق (للمدير فقط).
     */
    public function destroy(Comment $comment)
    {
        // 1. التحقق من صلاحية الحذف (Policy Check: Admin only)
        $this->authorize('delete', $comment);

        // 2. الحذف
        $comment->delete();
        
        return response()->json(['message' => 'تم حذف التعليق بنجاح.'], 200);
    }

    // ⭐ ملاحظة: وظيفة عرض التعليقات (index) يجب أن تتم من خلال PostController
    // حيث نقوم بتحميل (load) التعليقات الموافق عليها فقط عند عرض المقالة (Post show).
}