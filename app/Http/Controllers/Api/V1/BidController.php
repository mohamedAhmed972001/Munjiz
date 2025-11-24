<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Bid;
use App\Models\Project;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class BidController extends Controller
{
    // ⭐️ دالة تقديم عرض جديد (للمستقلين فقط)
    public function store(Request $request)
    {
        // ⚠️ تأكد أن المستخدم هو "مستقل"
        if (!$request->user()->hasRole('freelancer')) {
            return response()->json(['message' => 'Forbidden. Only freelancers can submit bids.'], 403);
        }

        // 1. قواعد التحقق
        $request->validate([
            // يجب أن يكون المشروع موجوداً ويكون حالته "open"
            'project_id' => 'required|uuid|exists:projects,id', 
            // المبلغ يجب أن يكون رقمي وأكبر من الصفر
            'amount' => 'required|numeric|min:1', 
            'cover_letter' => 'required|string|max:2000',
        ]);

        $projectId = $request->project_id;
        $freelancerId = Auth::id();

        // 2. منع تقديم أكثر من عرض (Check Unique Constraint)
        if (Bid::where('project_id', $projectId)->where('freelancer_id', $freelancerId)->exists()) {
            return response()->json(['message' => 'You have already submitted a bid for this project.'], 409);
        }

        // 3. إنشاء العرض
        $bid = Bid::create([
            'project_id' => $projectId,
            'freelancer_id' => $freelancerId,
            'amount' => $request->amount,
            'cover_letter' => $request->cover_letter,
            'status' => 'submitted',
        ]);

        return response()->json([
            'message' => 'Bid submitted successfully.',
            'bid' => $bid,
        ], 201);
    }

    // ⭐️ دالة قبول العرض (للعميل فقط)
    public function accept(Request $request, Bid $bid)
    {
        $project = $bid->project;
        $user = $request->user();

        // 1. ⚠️ تحقق من الصلاحيات: هل المستخدم هو صاحب المشروع؟
        if ($user->id !== $project->client_id) {
            return response()->json(['message' => 'Unauthorized action.'], 403);
        }

        // 2. تحقق من حالة المشروع: يجب أن يكون مفتوحاً
        if ($project->status !== 'open') {
            return response()->json(['message' => 'Project is not open for new selections.'], 400);
        }

        // 3. تنفيذ القبول (في Transaction)
        DB::transaction(function () use ($bid, $project) {

            // أ. تحديث حالة العرض إلى "مقبول"
            $bid->status = 'accepted';
            $bid->save();

            // ب. تحديث المشروع: تحديد الفائز وتغيير حالة المشروع
            $project->freelancer_id = $bid->freelancer_id;
            $project->status = 'in_progress';
            $project->save();

            // ج. رفض باقي العروض المقدمة على المشروع ده (تنظيف)
            Bid::where('project_id', $project->id)
                ->where('id', '!=', $bid->id)
                ->update(['status' => 'rejected']);
        });

        return response()->json([
            'message' => 'Bid accepted successfully. Project status updated to in_progress.',
            'project' => $project->fresh(), // جلب البيانات الجديدة للمشروع
        ]);
    }
}