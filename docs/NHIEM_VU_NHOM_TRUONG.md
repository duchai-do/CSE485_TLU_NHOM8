# NHIỆM VỤ NHÓM TRƯỞNG

## Đề tài

**Hệ thống quản lý ký túc xá và đăng ký chỗ ở**

## Nhánh thực hiện

```text
feature/student-registration
```

## Vai trò

Nhóm trưởng phụ trách:

- Quản lý tài khoản và hồ sơ sinh viên.
- Quản lý đăng ký chỗ ở.
- Thiết kế khung chung của dự án.
- Tổng hợp ERD và cấu trúc cơ sở dữ liệu.
- Quản lý nhánh `develop`.
- Review và tích hợp Pull Request của các thành viên.
- Hoàn thiện README, tài liệu và kịch bản demo.

---

# 1. Module cá nhân phụ trách

## Tên module

**Hồ sơ sinh viên và đăng ký chỗ ở**

## Các bảng phụ trách

1. `users`
2. `students`
3. `room_registrations`

## Các Model phụ trách

```text
app/Models/User.php
app/Models/Student.php
app/Models/RoomRegistration.php
```

## Controller phụ trách

```text
app/Http/Controllers/ProfileRegistration/StudentController.php
app/Http/Controllers/ProfileRegistration/RoomRegistrationController.php
```

## Form Request phụ trách

```text
app/Http/Requests/ProfileRegistration/StoreStudentRequest.php
app/Http/Requests/ProfileRegistration/UpdateStudentRequest.php
app/Http/Requests/ProfileRegistration/StoreRoomRegistrationRequest.php
app/Http/Requests/ProfileRegistration/UpdateRoomRegistrationRequest.php
```

## View phụ trách

```text
resources/views/profile-registration/students/
resources/views/profile-registration/registrations/
```

## Route phụ trách

```text
routes/modules/profile_registration.php
```

## Seeder phụ trách

```text
database/seeders/ProfileRegistrationSeeder.php
```

---

# 2. Chức năng cần thực hiện

## 2.1. Quản lý hồ sơ sinh viên

- Hiển thị danh sách sinh viên.
- Thêm hồ sơ sinh viên.
- Xem chi tiết hồ sơ sinh viên.
- Chỉnh sửa hồ sơ sinh viên.
- Khóa tài khoản sinh viên.
- Tìm kiếm sinh viên theo:
  - Mã sinh viên.
  - Họ và tên.
  - Lớp.
  - Khoa.
- Kiểm tra mã sinh viên không bị trùng.
- Kiểm tra email không bị trùng.
- Một tài khoản chỉ có một hồ sơ sinh viên.

## 2.2. Đăng ký chỗ ở

- Sinh viên tạo đơn đăng ký chỗ ở.
- Sinh viên xem danh sách đơn đã đăng ký.
- Sinh viên xem chi tiết đơn.
- Sinh viên chỉnh sửa đơn khi trạng thái là `pending`.
- Sinh viên hủy đơn khi trạng thái là `pending`.
- Cán bộ xem danh sách tất cả đơn đăng ký.
- Lọc đơn theo:
  - Học kỳ.
  - Năm học.
  - Trạng thái.
  - Loại phòng mong muốn.
- Hiển thị điểm và diện ưu tiên.
- Không cho một sinh viên tạo hai đơn trong cùng học kỳ.
- Không cho sửa đơn đã được duyệt, từ chối hoặc đã xếp phòng.

## 2.3. Trạng thái đơn đăng ký

```text
pending
approved
rejected
allocated
cancelled
```

Ý nghĩa:

| Trạng thái | Ý nghĩa |
|---|---|
| `pending` | Đang chờ cán bộ xét duyệt |
| `approved` | Đã được duyệt |
| `rejected` | Bị từ chối |
| `allocated` | Đã được xếp giường |
| `cancelled` | Sinh viên đã hủy đơn |

Lưu ý: chức năng **duyệt, từ chối và xếp giường** do thành viên phụ trách module `allocation-contract` thực hiện. Nhóm trưởng chỉ tạo dữ liệu và giao diện đăng ký ban đầu.

---

# 3. Cấu trúc bảng phụ trách

## 3.1. Bảng `users`

| Cột | Kiểu dữ liệu | Ràng buộc |
|---|---|---|
| `id` | bigint | Khóa chính |
| `name` | varchar | Không được để trống |
| `email` | varchar | Unique |
| `password` | varchar | Không được để trống |
| `role` | varchar | Mặc định `student` |
| `status` | boolean | Mặc định `true` |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

Các giá trị của `role`:

```text
student
staff
admin
```

Không xóa tài khoản đã phát sinh dữ liệu. Khi cần vô hiệu hóa, cập nhật:

```text
status = false
```

## 3.2. Bảng `students`

| Cột | Kiểu dữ liệu | Ràng buộc |
|---|---|---|
| `id` | bigint | Khóa chính |
| `user_id` | bigint | FK đến `users`, unique |
| `student_code` | varchar | Unique |
| `date_of_birth` | date | Có thể để trống |
| `gender` | varchar | Không được để trống |
| `class_name` | varchar | Có thể để trống |
| `faculty` | varchar | Có thể để trống |
| `phone` | varchar | Có thể để trống |
| `address` | text | Có thể để trống |
| `priority_type` | varchar | Có thể để trống |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

Quan hệ:

```text
users 1 — 1 students
```

## 3.3. Bảng `room_registrations`

| Cột | Kiểu dữ liệu | Ràng buộc |
|---|---|---|
| `id` | bigint | Khóa chính |
| `student_id` | bigint | FK đến `students` |
| `semester` | varchar | Không được để trống |
| `academic_year` | varchar | Không được để trống |
| `preferred_room_type` | varchar | Có thể để trống |
| `priority_score` | integer | Mặc định `0` |
| `status` | varchar | Mặc định `pending` |
| `note` | text | Có thể để trống |
| `reviewed_by` | bigint | FK đến `users`, nullable |
| `reviewed_at` | datetime | Nullable |
| `rejection_reason` | text | Nullable |
| `created_at` | timestamp | |
| `updated_at` | timestamp | |

Ràng buộc duy nhất:

```text
unique(student_id, semester, academic_year)
```

Quan hệ:

```text
students 1 — N room_registrations
users 1 — N room_registrations (reviewed_by)
```

---

# 4. Quan hệ trong Model

## `User.php`

```php
public function student()
{
    return $this->hasOne(Student::class);
}

public function reviewedRegistrations()
{
    return $this->hasMany(RoomRegistration::class, 'reviewed_by');
}
```

## `Student.php`

```php
public function user()
{
    return $this->belongsTo(User::class);
}

public function roomRegistrations()
{
    return $this->hasMany(RoomRegistration::class);
}
```

## `RoomRegistration.php`

```php
public function student()
{
    return $this->belongsTo(Student::class);
}

public function reviewer()
{
    return $this->belongsTo(User::class, 'reviewed_by');
}
```

---

# 5. Route dự kiến

File:

```text
routes/modules/profile_registration.php
```

Khung route:

```php
<?php

use App\Http\Controllers\ProfileRegistration\RoomRegistrationController;
use App\Http\Controllers\ProfileRegistration\StudentController;
use Illuminate\Support\Facades\Route;

Route::prefix('staff')
    ->name('staff.')
    ->group(function () {
        Route::resource('students', StudentController::class);
        Route::get(
            'room-registrations',
            [RoomRegistrationController::class, 'index']
        )->name('room-registrations.index');

        Route::get(
            'room-registrations/{roomRegistration}',
            [RoomRegistrationController::class, 'show']
        )->name('room-registrations.show');
    });

Route::prefix('student')
    ->name('student.')
    ->group(function () {
        Route::resource(
            'room-registrations',
            RoomRegistrationController::class
        );
    });
```

Phần phân quyền bằng middleware sẽ được bổ sung sau khi nhóm hoàn thiện đăng nhập.

---

# 6. Migration cần tạo

```bash
php artisan make:migration add_role_and_status_to_users_table --table=users
php artisan make:model Student -m
php artisan make:model RoomRegistration -m
```

Thứ tự migration:

```text
users
→ students
→ room_registrations
```

Sau khi hoàn thiện migration:

```bash
php artisan migrate:fresh
```

Khi có Seeder:

```bash
php artisan migrate:fresh --seed
```

---

# 7. File cần tạo trên nhánh cá nhân

```text
app/
├── Http/
│   ├── Controllers/
│   │   └── ProfileRegistration/
│   │       ├── StudentController.php
│   │       └── RoomRegistrationController.php
│   └── Requests/
│       └── ProfileRegistration/
│           ├── StoreStudentRequest.php
│           ├── UpdateStudentRequest.php
│           ├── StoreRoomRegistrationRequest.php
│           └── UpdateRoomRegistrationRequest.php
└── Models/
    ├── Student.php
    └── RoomRegistration.php

database/
├── migrations/
│   ├── xxxx_add_role_and_status_to_users_table.php
│   ├── xxxx_create_students_table.php
│   └── xxxx_create_room_registrations_table.php
└── seeders/
    └── ProfileRegistrationSeeder.php

resources/views/profile-registration/
├── students/
│   ├── index.blade.php
│   ├── create.blade.php
│   ├── edit.blade.php
│   └── show.blade.php
└── registrations/
    ├── index.blade.php
    ├── create.blade.php
    ├── edit.blade.php
    └── show.blade.php

routes/modules/
└── profile_registration.php
```

---

# 8. Công việc chung của nhóm trưởng

Ngoài module cá nhân, nhóm trưởng cần:

- Chốt phạm vi đề tài.
- Tổng hợp bảng phân công.
- Tổng hợp ERD/schema.
- Thống nhất tên bảng, tên cột và trạng thái.
- Tạo và quản lý nhánh `develop`.
- Kiểm tra các thành viên tạo nhánh từ `develop`.
- Review Pull Request.
- Kiểm tra khóa ngoại giữa các module.
- Giải quyết conflict khi merge.
- Tích hợp Seeder.
- Kiểm tra toàn bộ project bằng:

```bash
php artisan migrate:fresh --seed
php artisan route:list
php artisan test
```

- Hoàn thiện:
  - `README.md`
  - Tài khoản demo.
  - Kịch bản demo.
  - Slide thuyết trình.
  - Hướng dẫn cài đặt.

---

# 9. Quy tắc Git cho nhánh nhóm trưởng

Chuyển sang nhánh:

```bash
git checkout develop
git pull origin develop

git checkout feature/student-registration
git merge develop
```

Sau khi làm xong:

```bash
git status
git add .
git commit -m "feat: add student profile and room registration module"
git push origin feature/student-registration
```

Tạo Pull Request:

```text
feature/student-registration
→ develop
```

Không merge trực tiếp vào `main`.

---

# 10. Checklist Buổi 13 của nhóm trưởng

- [ ] Có file phân công nhiệm vụ.
- [ ] Có ERD/schema chung.
- [ ] Chốt tên 12 bảng.
- [ ] Chốt khóa ngoại giữa các bảng.
- [ ] Chốt trạng thái đơn đăng ký.
- [ ] Có nhánh `develop`.
- [ ] Có nhánh `feature/student-registration`.
- [ ] Tạo migration bổ sung `users`.
- [ ] Tạo migration `students`.
- [ ] Tạo migration `room_registrations`.
- [ ] Tạo Model và relationship.
- [ ] Chạy được `php artisan migrate:fresh`.
- [ ] Commit và push đúng nhánh.
- [ ] Tạo Pull Request sang `develop`.

---

# 11. Tiêu chí hoàn thành module

Module của nhóm trưởng hoàn thành khi:

- Migration chạy không lỗi.
- Có đầy đủ khóa ngoại.
- Có validation backend.
- Có CRUD hồ sơ sinh viên.
- Có chức năng đăng ký chỗ ở.
- Không tạo được đơn trùng học kỳ.
- Không sửa được đơn đã xử lý.
- Có Seeder tạo tài khoản và hồ sơ mẫu.
- Có commit riêng.
- Có Pull Request riêng.
- Có thể tự demo và giải thích toàn bộ phần code phụ trách.
