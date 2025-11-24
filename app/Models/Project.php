<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // ⬅️ متنساش دي

class Project extends Model
{
  use HasFactory, HasUuids;

  protected $fillable = [
    'client_id',
    'title',
    'description',
    'budget',
    'status',
    'freelancer_id',
  ];

  // علاقة عكسية: العميل الذي أنشأ المشروع
  public function client()
  {
    return $this->belongsTo(User::class, 'client_id');
  }

  // علاقة عكسية: المستقل الفائز بالمشروع
  public function freelancer()
  {
    return $this->belongsTo(User::class, 'freelancer_id');
  }

  // علاقة: عروض الأسعار المقدمة على هذا المشروع
  public function bids()
  {
    return $this->hasMany(Bid::class);
  }
  // ... داخل الكلاس

  // التقييمات المتعلقة بالمشروع (غالباً تقييم واحد من العميل وواحد من المستقل)
  public function reviews()
  {
    return $this->hasMany(Review::class);
  }
  // ... داخل الكلاس

  // كل الرسائل المتعلقة بالمشروع ده
  public function messages()
  {
    // ترتيب الرسائل من الأقدم للأحدث (الأفضل في الشات)
    return $this->hasMany(Message::class)->latest();
  }
}
