<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // ⬅️ متنساش دي

class Message extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'project_id',
        'sender_id',
        'content',
    ];

    // من قام بإرسال الرسالة
    public function sender()
    {
        return $this->belongsTo(User::class, 'sender_id');
    }

    // المشروع الذي تتبعه المحادثة
    public function project()
    {
        return $this->belongsTo(Project::class);
    }
}