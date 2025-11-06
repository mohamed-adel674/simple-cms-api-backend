<?php

namespace App\Http\Controllers;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Str;

class PostController extends Controller
{
    // الدالة 1: عرض جميع المقالات (للعامة)
    public function index()
    {
        // 1. التحقق من صلاحية العرض (ViewAny)
        // هذا التحقق يضمن أن المستخدم الحالي (إن وجد) لديه القدرة على طلب القائمة.
        Gate::authorize('viewAny', Post::class);

        // 2. فلترة المقالات للعرض العام
        // يجب عرض المنشورات (published) فقط لغير المسجلين أو إذا لم يكن الطلب من Admin
        $posts = Post::with('category', 'user')
            ->when(!Auth::check() || Auth::user()->role !== 'admin', function ($query) {
                return $query->where('status', 'published');
            })
            ->latest()
            ->paginate(10);

        return response()->json($posts, 200);
    }

    // الدالة 2: عرض مقالة محددة
    public function show(Post $post)
    {
        // 1. التحقق من صلاحية العرض (View) باستخدام الـ Policy
        $this->authorize('view', $post);

        // 2. إرجاع المقالة مع بيانات الكاتب والتصنيف والتعليقات الموافق عليها
        return response()->json($post->load([
            'category',
            'user',
            'comments' => function ($query) {
                $query->where('approved', true) // ⭐ فلترة التعليقات الموافق عليها فقط
                    ->with('user'); // تحميل بيانات المستخدم الذي علق
            }
        ]), 200);
    }

    // الدالة 3: إنشاء مقالة جديدة
    public function store(Request $request)
    {
        // 1. التحقق من صلاحية الإنشاء (Create) - سيتم تمرير المستخدم إلى Policy
        $this->authorize('create', Post::class);

        // 2. التحقق من صحة البيانات
        $fields = $request->validate([
            'title' => 'required|string|unique:posts,title',
            'body' => 'required|string',
            'category_id' => 'required|exists:categories,id',
            'status' => 'required|in:draft,published',
        ]);

        // 3. تطبيق تنقية HTML (Sanitization) على المحتوى
        // نستخدم دالة clean() المفترضة من حزمة مثل voku/html-purifier
        $cleanedBody = clean($fields['body']);

        // 4. إنشاء المقالة (مع Slug آليًا)
        $post = Post::create([
            'user_id' => Auth::id(), // ربط المقالة بالمستخدم الحالي
            'category_id' => $fields['category_id'],
            'title' => $fields['title'],
            'slug' => Str::slug($fields['title']), // إنشاء slug
            'body' => $cleanedBody,
            'status' => $fields['status'],
        ]);

        return response()->json(['message' => 'تم إنشاء المقالة بنجاح.', 'post' => $post], 201);
    }

    // الدالة 4: تحديث مقالة
    public function update(Request $request, Post $post)
    {
        // 1. التحقق من صلاحية التحديث (Update) - (المالك فقط أو Admin)
        $this->authorize('update', $post);

        // 2. التحقق من صحة البيانات (تجاهل عنوان المقالة الحالية للـ Unique Rule)
        $fields = $request->validate([
            'title' => 'sometimes|required|string|unique:posts,title,' . $post->id,
            'body' => 'sometimes|required|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'status' => 'sometimes|required|in:draft,published',
        ]);

        // 3. تطبيق تنقية HTML وتحديث المقالة
        if (isset($fields['body'])) {
            $fields['body'] = clean($fields['body']);
        }
        if (isset($fields['title'])) {
            $fields['slug'] = Str::slug($fields['title']);
        }

        $post->update($fields);

        return response()->json(['message' => 'تم تحديث المقالة بنجاح.', 'post' => $post], 200);
    }

    // الدالة 5: حذف مقالة
    public function destroy(Post $post)
    {
        // 1. التحقق من صلاحية الحذف (Delete) - (المالك فقط أو Admin)
        $this->authorize('delete', $post);

        // 2. الحذف
        $post->delete();

        return response()->json(['message' => 'تم حذف المقالة بنجاح.'], 200);
    }
}
