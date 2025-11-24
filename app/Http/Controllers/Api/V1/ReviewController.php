<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Project;
use App\Models\Review;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ReviewController extends Controller
{
    // دالة إنشاء تقييم جديد
    public function store(Request $request)
    {
        // 1. قواعد التحقق الأساسية
        $request->validate([
            'project_id' => 'required|uuid|exists:projects,id',
            'rating' => 'required|integer|min:1|max:5', // التقييم من 1 لـ 5 نجوم
            'comment' => 'nullable|string|max:1000',
        ]);

        $project = Project::find($request->project_id);
        $user = Auth::user();

        // 2. ⚠️ التحقق من حالة المشروع (يجب أن يكون مكتمل)
        if ($project->status !== 'completed') {
            return response()->json(['message' => 'Cannot review a project that is not yet completed.'], 400);
        }

        // 3. تحديد دور المستخدم والطرف المُقَيَّم
        $reviewerId = $user->id;
        $reviewedId = null;

        // إذا كان المستخدم هو العميل، فهو يقيم المستقل الفائز
        if ($user->id === $project->client_id) {
            $reviewedId = $project->freelancer_id;
        } 
        // إذا كان المستخدم هو المستقل الفائز، فهو يقيم العميل
        elseif ($user->id === $project->freelancer_id) {
            $reviewedId = $project->client_id;
        } 
        // إذا كان المستخدم ليس طرفاً في المشروع، نمنعه
        else {
            return response()->json(['message' => 'Unauthorized. You are not a party to this project.'], 403);
        }
        
        // 4. منع التقييم المكرر (Unique Constraint)
        if (Review::where('reviewer_id', $reviewerId)->where('project_id', $project->id)->exists()) {
            return response()->json(['message' => 'You have already submitted a review for this project.'], 409);
        }

        // 5. إنشاء التقييم
        $review = Review::create([
            'reviewer_id' => $reviewerId,
            'reviewed_id' => $reviewedId,
            'project_id' => $project->id,
            'rating' => $request->rating,
            'comment' => $request->comment,
        ]);

        return response()->json([
            'message' => 'Review submitted successfully.',
            'review' => $review,
        ], 201);
    }
}