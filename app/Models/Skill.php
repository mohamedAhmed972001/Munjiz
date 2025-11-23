<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids;

class Skill extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = ['name'];

    // لو حبينا نعرف مين اليوزرز اللي عندهم مهارة معينة
    public function profiles()
    {
        return $this->belongsToMany(Profile::class, 'profile_skill');
    }
}