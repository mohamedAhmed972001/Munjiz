<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;

class ProjectController extends Controller
{
    // ⭐️ دالة إنشاء مشروع جديد
    public function store(Request $request)
    {
        // ⚠️ تأكد أن المستخدم هو "عميل"
        if (!$request->user()->hasRole('client')) {
            return response()->json(['message' => 'Forbidden. Only clients can create projects.'], 403);
        }

        // 1. قواعد التحقق
        $request->validate([
            'title' => 'required|string|max:255',
            'description' => 'required|string',
            // الميزانية يجب أن تكون رقماً أكبر من 100
            'budget' => 'required|numeric|min:100', 
        ]);

        // 2. إنشاء المشروع
        $project = Project::create([
            // يتم إسناد الـ client_id مباشرة من المستخدم الحالي
            'client_id' => Auth::id(), 
            'title' => $request->title,
            'description' => $request->description,
            'budget' => $request->budget,
            'status' => 'open', // المشروع يبدأ كـ "مفتوح"
        ]);

        return response()->json([
            'message' => 'Project created successfully and is now open for bids.',
            'project' => $project,
        ], 201);
    }

    // ⭐️ دالة عرض كل المشاريع المفتوحة (لجميع المستقلين)
    public function index(Request $request)
    {
        // نعرض المشاريع المفتوحة فقط والمطلوبة من المستخدمين
        $projects = Project::where('status', 'open')
            // نحمل اسم العميل وعدد العروض المقدمة
            ->withCount('bids') 
            ->with('client:id,name') 
            ->get();

        return response()->json($projects);
    }

    // ⭐️ دالة عرض مشروع معين بالتفاصيل
    public function show(Project $project)
    {
        // نعرض تفاصيل المشروع مع كل عروض الأسعار المقدمة عليه
        $project->load(['client:id,name', 'bids.freelancer:id,name']); 

        return response()->json($project);
    }
// ... داخل الكلاس ProjectController

// ⭐️ دالة لإكمال المشروع (Completed)
public function complete(Request $request, Project $project)
{
    $user = $request->user();

    // 1. التحقق من الصلاحيات: يجب أن يكون المستخدم هو العميل صاحب المشروع
    if ($user->id !== $project->client_id) {
        return response()->json(['message' => 'Unauthorized. Only the client can mark the project as complete.'], 403);
    }

    // 2. التحقق من حالة المشروع: يجب أن يكون قيد التنفيذ
    if ($project->status !== 'in_progress') {
        return response()->json(['message' => 'Project must be "in_progress" to be marked as complete.'], 400);
    }
    
    // 3. تحديث الحالة
    $project->status = 'completed';
    $project->save();

    return response()->json([
        'message' => 'Project marked as completed. Ready for review.',
        'project' => $project,
    ]);
}

    // باقي دوال الـ resource (update, destroy) يمكن إضافتها لاحقاً للتعديل أو الإلغاء
}
