<?php

namespace App\Models;

use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * Các cột được phép thêm dữ liệu bằng create() hoặc update().
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
        'status',
    ];

    /**
     * Các cột không hiển thị khi chuyển dữ liệu sang mảng hoặc JSON.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Ép kiểu dữ liệu của các cột.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'status' => 'boolean',
        ];
    }

    /**
     * Một tài khoản sinh viên có một hồ sơ sinh viên.
     */
    public function student(): HasOne
    {
        return $this->hasOne(Student::class);
    }

    /**
     * Một cán bộ có thể xét duyệt nhiều đơn đăng ký.
     */
    public function reviewedRegistrations(): HasMany
    {
        return $this->hasMany(
            RoomRegistration::class,
            'reviewed_by'
        );
    }

    /**
     * Kiểm tra tài khoản có phải sinh viên không.
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Kiểm tra tài khoản có phải cán bộ không.
     */
    public function isStaff(): bool
    {
        return $this->role === 'staff';
    }

    /**
     * Kiểm tra tài khoản có phải quản trị viên không.
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }
}