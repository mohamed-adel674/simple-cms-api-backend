<?php

namespace App\Http\Controllers;

use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // الدالة 1: عرض جميع التصنيفات
    public function index()
    {
        // لا نحتاج لـ Gate هنا لأن الـ Policy يسمح للجميع بالعرض (ViewAny)
        $categories = Category::withCount('posts') // عرض عدد المقالات في كل تصنيف
                            ->orderBy('name')
                            ->get();

        return response()->json($categories, 200);
    }

    // الدالة 2: عرض تصنيف محدد ومقالاته المنشورة
    public function show(Category $category)
    {
        // لا نحتاج لـ Gate هنا لأن الـ Policy يسمح للجميع بالعرض (View)
        $category->load(['posts' => function ($query) {
            $query->where('status', 'published') // فقط المقالات المنشورة
                  ->latest();
        }]);

        return response()->json($category, 200);
    }

    // الدالة 3: إنشاء تصنيف جديد (يتطلب دور Admin)
    public function store(Request $request)
    {
        // 1. التحقق من صلاحية الإنشاء (Policy Check: Admin only)
        $this->authorize('create', Category::class);

        // 2. التحقق من صحة البيانات
        $fields = $request->validate([
            'name' => 'required|string|unique:categories,name|max:255',
        ]);

        // 3. إنشاء التصنيف (مع Slug آليًا)
        $category = Category::create([
            'name' => $fields['name'],
            'slug' => Str::slug($fields['name']),
        ]);

        return response()->json(['message' => 'تم إنشاء التصنيف بنجاح.', 'category' => $category], 201);
    }

    // الدالة 4: تحديث تصنيف (يتطلب دور Admin)
    public function update(Request $request, Category $category)
    {
        // 1. التحقق من صلاحية التحديث (Policy Check: Admin only)
        $this->authorize('update', $category);

        // 2. التحقق من صحة البيانات (تجاهل اسم التصنيف الحالي للـ Unique Rule)
        $fields = $request->validate([
            'name' => 'sometimes|required|string|unique:categories,name,' . $category->id . '|max:255',
        ]);

        // 3. تحديث التصنيف والـ Slug
        if (isset($fields['name'])) {
            $category->update([
                'name' => $fields['name'],
                'slug' => Str::slug($fields['name']),
            ]);
        }

        return response()->json(['message' => 'تم تحديث التصنيف بنجاح.', 'category' => $category], 200);
    }

    // الدالة 5: حذف تصنيف (يتطلب دور Admin)
    public function destroy(Category $category)
    {
        // 1. التحقق من صلاحية الحذف (Policy Check: Admin only)
        $this->authorize('delete', $category);

        // 2. الحذف
        $category->delete();
        
        return response()->json(['message' => 'تم حذف التصنيف بنجاح.'], 200);
    }
}