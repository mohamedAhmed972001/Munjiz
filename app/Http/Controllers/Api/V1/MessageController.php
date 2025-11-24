<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MessageController extends Controller
{
  // ... داخل الكلاس MessageController

  // لإرسال رسالة جديدة
  public function store(Request $request)
  {
    $request->validate([
      'project_id' => 'required|uuid|exists:projects,id',
      'content' => 'required|string|max:500',
    ]);

    $project = Project::find($request->project_id);
    $user = $request->user();

    // 1. التحقق من الصلاحيات (نفس شرط العرض)
    if ($user->id !== $project->client_id && $user->id !== $project->freelancer_id) {
      return response()->json(['message' => 'Unauthorized. You cannot send messages in this project.'], 403);
    }

    // 2. التحقق من حالة المشروع (يجب أن يكون in_progress أو completed)
    if ($project->status !== 'in_progress' && $project->status !== 'completed') {
      return response()->json(['message' => 'Chat is only available for active or completed projects.'], 400);
    }


    // 3. إنشاء الرسالة
    $message = Message::create([
      'project_id' => $project->id,
      'sender_id' => $user->id,
      'content' => $request->content,
    ]);

    // جلب تفاصيل المرسل للرد (للتسهيل على الواجهة الأمامية)
    $message->load('sender:id,name');

    return response()->json([
      'message' => 'Message sent successfully.',
      'data' => $message,
    ], 201);
  }
  // ... داخل الكلاس MessageController

  // لعرض كل رسائل مشروع معين
  public function index(Request $request, Project $project)
  {
    $user = $request->user();

    // التحقق من الصلاحيات: يجب أن يكون المستخدم هو العميل أو المستقل الفائز في المشروع
    if ($user->id !== $project->client_id && $user->id !== $project->freelancer_id) {
      return response()->json(['message' => 'Unauthorized. You are not a party to this project\'s chat.'], 403);
    }

    // جلب الرسائل وترتيبها وإظهار المرسل
    $messages = $project->messages()->with('sender:id,name')->get();

    return response()->json([
      'messages' => $messages,
    ]);
  }
}
