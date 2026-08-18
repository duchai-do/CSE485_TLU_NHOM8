<?php

namespace Database\Seeders;

use App\Models\RoomRegistration;
use App\Models\Student;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::create([
            'name' => 'Quản trị viên',
            'email' => 'admin@tlu.edu.vn',
            'password' => '12345678',
            'role' => 'admin',
            'status' => true,
        ]);

        $staff = User::create([
            'name' => 'Cán bộ ký túc xá',
            'email' => 'staff@tlu.edu.vn',
            'password' => '12345678',
            'role' => 'staff',
            'status' => true,
        ]);

        $samples = [
            ['Nguyễn Văn An', 'an@tlu.edu.vn', 'SV001', '2005-03-15', 'Nam', '65HTTT1', null],
            ['Trần Thị Bình', 'binh@tlu.edu.vn', 'SV002', '2005-07-20', 'Nữ', '65CNTT2', 'Hộ nghèo'],
            ['Lê Minh Cường', 'cuong@tlu.edu.vn', 'SV003', '2004-11-02', 'Nam', '65PM1', 'Con thương binh'],
        ];

        $students = collect($samples)->map(function ($row, $index) {
            $user = User::create([
                'name' => $row[0],
                'email' => $row[1],
                'password' => '12345678',
                'role' => 'student',
                'status' => true,
            ]);

            return Student::create([
                'user_id' => $user->id,
                'student_code' => $row[2],
                'date_of_birth' => $row[3],
                'gender' => $row[4],
                'class_name' => $row[5],
                'faculty' => 'Công nghệ thông tin',
                'phone' => '090000000'.($index + 1),
                'address' => ['Hà Nội', 'Nam Định', 'Thanh Hóa'][$index],
                'priority_type' => $row[6],
            ]);
        });

        RoomRegistration::create([
            'student_id' => $students[0]->id,
            'semester' => '1',
            'academic_year' => '2026-2027',
            'preferred_room_type' => 'Phòng 4 người',
            'priority_score' => 0,
            'status' => 'pending',
            'note' => 'Mong muốn ở gần khu học tập.',
        ]);

        RoomRegistration::create([
            'student_id' => $students[1]->id,
            'semester' => '1',
            'academic_year' => '2026-2027',
            'preferred_room_type' => 'Phòng 6 người',
            'priority_score' => 10,
            'status' => 'approved',
            'note' => 'Sinh viên thuộc diện ưu tiên.',
            'reviewed_by' => $staff->id,
            'reviewed_at' => now(),
        ]);

        RoomRegistration::create([
            'student_id' => $students[2]->id,
            'semester' => '1',
            'academic_year' => '2026-2027',
            'preferred_room_type' => 'Phòng 4 người',
            'priority_score' => 8,
            'status' => 'rejected',
            'note' => 'Đăng ký chỗ ở học kỳ 1.',
            'reviewed_by' => $admin->id,
            'reviewed_at' => now(),
            'rejection_reason' => 'Tạm thời chưa còn loại phòng phù hợp.',
        ]);
    }
}
