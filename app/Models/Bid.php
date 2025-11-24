<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // ⬅️ متنساش دي

class Bid extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'project_id',
        'freelancer_id',
        'amount',
        'cover_letter',
        'status',
    ];

    // علاقة عكسية: المشروع الذي يخصه هذا العرض
    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    // علاقة عكسية: المستقل الذي قدم هذا العرض
    public function freelancer()
    {
        return $this->belongsTo(User::class, 'freelancer_id');
    }
}