<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Student extends Model
{
    use HasFactory;

    protected $fillable = [
        'user_id',
        'student_code',
        'date_of_birth',
        'gender',
        'class_name',
        'faculty',
        'phone',
        'address',
        'priority_type',
    ];

    protected function casts(): array
    {
        return [
            'date_of_birth' => 'date',
        ];
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function roomRegistrations()
    {
        return $this->hasMany(RoomRegistration::class);
    }
}
