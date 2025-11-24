<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // ⬅️ متنساش دي

class Review extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'reviewer_id',
        'reviewed_id',
        'project_id',
        'rating',
        'comment',
    ];

    // من قام بالتقييم (العميل أو المستقل)
    public function reviewer()
    {
        return $this->belongsTo(User::class, 'reviewer_id');
    }

    // من تم تقييمه (المستقل أو العميل)
    public function reviewed()
    {
        return $this->belongsTo(User::class, 'reviewed_id');
    }
    
    // المشروع الذي تم بناءً عليه التقييم
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}