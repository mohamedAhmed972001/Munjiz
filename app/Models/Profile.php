<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // ⬅️ متنساش دي
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Profile extends Model implements HasMedia
{
    use HasFactory, HasUuids, InteractsWithMedia;

    protected $fillable = [
        'user_id', 
        'job_title', 
        'bio', 
        'github_link', 
        'linkedin_link',
        'wallet_balance'
    ];

    // علاقة عكسية مع اليوزر
    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // علاقة مع المهارات (Many-to-Many)
    public function skills()
    {
        return $this->belongsToMany(Skill::class, 'profile_skill');
    }
    
    // دالة مساعدة (Helper) عشان ترجع لينك الصورة مباشرة للفرونت
    public function getAvatarUrlAttribute()
    {
        return $this->getFirstMediaUrl('avatar') ?: null;
    }
}