<?php

namespace App\Models;

use App\Models\Profile;
use App\Models\Project;
use App\Models\Bid;
// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Database\Eloquent\Concerns\HasUuids; // ⬅️ Trait الـ UUID الرسمي

class User extends Authenticatable
{
  use HasFactory, Notifiable, HasApiTokens, HasRoles, HasUuids;

  /**
   * The attributes that are mass assignable.
   *
   * @var array<int, string>
   */
  protected $fillable = [
    'name',
    'email',
    'password',
  ];

  /**
   * The attributes that should be hidden for serialization.
   *
   * @var array<int, string>
   */
  protected $hidden = [
    'password',
    'remember_token',
  ];

  /**
   * Get the attributes that should be cast.
   *
   * @return array<string, string>
   */
  protected function casts(): array
  {
    return [
      'email_verified_at' => 'datetime',
      'password' => 'hashed',
    ];
  }
  public function profile()
  {
    return $this->hasOne(Profile::class);
  }
  // المشاريع التي أنشأها المستخدم (كعميل)
  public function postedProjects()
  {
    return $this->hasMany(Project::class, 'client_id');
  }

  // المشاريع التي تم تعيين المستخدم للعمل عليها (كمستقل فائز)
  public function assignedProjects()
  {
    return $this->hasMany(Project::class, 'freelancer_id');
  }

  // عروض الأسعار التي قدمها المستخدم (كمستقل)
  public function bids()
  {
    return $this->hasMany(Bid::class, 'freelancer_id');
  }
  // ... داخل الكلاس

  // التقييمات التي تلقاها المستخدم (Reviewed)
  public function receivedReviews()
  {
    return $this->hasMany(Review::class, 'reviewed_id');
  }

  // التقييمات التي قام بها المستخدم (Reviewer)
  public function givenReviews()
  {
    return $this->hasMany(Review::class, 'reviewer_id');
  }

  // دالة لحساب متوسط التقييمات (ميزة إضافية)
  public function averageRating()
  {
    return $this->receivedReviews()->avg('rating');
  }
  // ... داخل الكلاس

  // الرسائل التي أرسلها المستخدم
  public function messages()
  {
    return $this->hasMany(Message::class, 'sender_id');
  }
}
