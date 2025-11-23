<?php

namespace App\Http\Controllers\Api\V1;

use App\Http\Controllers\Controller;
use App\Models\Skill;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ProfileController extends Controller
{
    // عرض البروفايل
    public function show(Request $request)
    {
        // بنجيب اليوزر مع بروفايله ومهاراته في استعلام واحد (Eager Loading)
        $user = $request->user()->load(['profile.skills']);

        return response()->json([
            'name' => $user->name,
            'email' => $user->email,
            'role' => $user->getRoleNames()->first(),
            'job_title' => $user->profile->job_title,
            'bio' => $user->profile->bio,
            'avatar' => $user->profile->avatar_url, // الـ Attribute اللي عملناه
            'github_link' => $user->profile->github_link,
            'linkedin_link' => $user->profile->linkedin_link,
            'wallet_balance' => $user->profile->wallet_balance,
            'skills' => $user->profile->skills->pluck('name'), // بنرجع أسماء المهارات بس
        ]);
    }

    // تحديث البروفايل
    public function update(Request $request)
    {
        $request->validate([
            'job_title' => 'required|string|max:100',
            'bio' => 'nullable|string|max:1000',
            'github_link' => 'nullable|url',
            'linkedin_link' => 'nullable|url',
            'avatar' => 'nullable|image|max:2048', // صورة بحد أقصى 2 ميجا
            'skills' => 'nullable|array',
            'skills.*' => 'string|exists:skills,name' // لازم المهارة تكون موجودة في جدول المهارات
        ]);

        $user = $request->user();
        $profile = $user->profile;

        // نفتح Transaction عشان نضمن إن كل حاجة تتحفظ أو مفيش حاجة تتحفظ
        DB::transaction(function () use ($request, $profile) {
            
            // 1. تحديث البيانات النصية
            $profile->update([
                'job_title' => $request->job_title,
                'bio' => $request->bio,
                'github_link' => $request->github_link,
                'linkedin_link' => $request->linkedin_link,
            ]);

            // 2. تحديث المهارات
            if ($request->has('skills')) {
                // بنجيب الـ IDs بتاعت المهارات دي
                $skillIds = Skill::whereIn('name', $request->skills)->pluck('id');
                // بنعمل Sync (بيمسح القديم ويحط الجديد)
                $profile->skills()->sync($skillIds);
            }

            // 3. رفع الصورة (Spatie Media Library Magic 🪄)
            if ($request->hasFile('avatar')) {
                // المكتبة ذكية: هتمسح القديمة وتضيف الجديدة وتحطها في فولدر وتعمل كل حاجة
                $profile->clearMediaCollection('avatar');
                $profile->addMediaFromRequest('avatar')
                        ->toMediaCollection('avatar');
            }
        });

        return response()->json([
            'message' => 'Profile updated successfully',
            'avatar_url' => $profile->refresh()->avatar_url, // بنرجع رابط الصورة الجديد
        ]);
    }
}