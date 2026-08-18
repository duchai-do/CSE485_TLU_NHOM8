<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $now = now();

        // =========================================================
        // 1. USERS - Thành viên 1
        // =========================================================
        DB::table('users')->insert([
            [
                'name' => 'Quản trị viên',
                'email' => 'admin@tlu.edu.vn',
                'email_verified_at' => $now,
                'password' => Hash::make('12345678'),
                'role' => 'admin',
                'status' => true,
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Nguyễn Văn An',
                'email' => 'an@tlu.edu.vn',
                'email_verified_at' => $now,
                'password' => Hash::make('12345678'),
                'role' => 'student',
                'status' => true,
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Trần Thị Bình',
                'email' => 'binh@tlu.edu.vn',
                'email_verified_at' => $now,
                'password' => Hash::make('12345678'),
                'role' => 'student',
                'status' => true,
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'name' => 'Lê Minh Cường',
                'email' => 'cuong@tlu.edu.vn',
                'email_verified_at' => $now,
                'password' => Hash::make('12345678'),
                'role' => 'student',
                'status' => true,
                'remember_token' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // =========================================================
        // 2. STUDENTS - Thành viên 1
        // =========================================================
        $anUserId = DB::table('users')
            ->where('email', 'an@tlu.edu.vn')
            ->value('id');

        $binhUserId = DB::table('users')
            ->where('email', 'binh@tlu.edu.vn')
            ->value('id');

        $cuongUserId = DB::table('users')
            ->where('email', 'cuong@tlu.edu.vn')
            ->value('id');

        DB::table('students')->insert([
            [
                'user_id' => $anUserId,
                'student_code' => 'SV001',
                'date_of_birth' => '2005-03-15',
                'gender' => 'Nam',
                'class_name' => '65HTTT1',
                'faculty' => 'Công nghệ thông tin',
                'phone' => '0900000001',
                'address' => 'Hà Nội',
                'priority_type' => 'Không',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $binhUserId,
                'student_code' => 'SV002',
                'date_of_birth' => '2005-07-20',
                'gender' => 'Nữ',
                'class_name' => '65CNTT2',
                'faculty' => 'Công nghệ thông tin',
                'phone' => '0900000002',
                'address' => 'Nam Định',
                'priority_type' => 'Hộ nghèo',
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'user_id' => $cuongUserId,
                'student_code' => 'SV003',
                'date_of_birth' => '2004-11-02',
                'gender' => 'Nam',
                'class_name' => '65PM1',
                'faculty' => 'Công nghệ thông tin',
                'phone' => '0900000003',
                'address' => 'Thanh Hóa',
                'priority_type' => 'Con thương binh',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // =========================================================
        // 3. ROOM REGISTRATIONS - Thành viên 1
        // =========================================================
        $anStudentId = DB::table('students')
            ->where('student_code', 'SV001')
            ->value('id');

        $binhStudentId = DB::table('students')
            ->where('student_code', 'SV002')
            ->value('id');

        $cuongStudentId = DB::table('students')
            ->where('student_code', 'SV003')
            ->value('id');

        $adminId = DB::table('users')
            ->where('email', 'admin@tlu.edu.vn')
            ->value('id');

        DB::table('room_registrations')->insert([
            [
                'student_id' => $anStudentId,
                'semester' => '1',
                'academic_year' => '2026-2027',
                'preferred_room_type' => 'Phòng 4 người',
                'priority_score' => 0,
                'status' => 'pending',
                'note' => 'Mong muốn ở gần khu học tập.',
                'reviewed_by' => null,
                'reviewed_at' => null,
                'rejection_reason' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'student_id' => $binhStudentId,
                'semester' => '1',
                'academic_year' => '2026-2027',
                'preferred_room_type' => 'Phòng 6 người',
                'priority_score' => 10,
                'status' => 'approved',
                'note' => 'Sinh viên thuộc diện ưu tiên.',
                'reviewed_by' => $adminId,
                'reviewed_at' => $now,
                'rejection_reason' => null,
                'created_at' => $now,
                'updated_at' => $now,
            ],
            [
                'student_id' => $cuongStudentId,
                'semester' => '1',
                'academic_year' => '2026-2027',
                'preferred_room_type' => 'Phòng 4 người',
                'priority_score' => 8,
                'status' => 'rejected',
                'note' => 'Đăng ký chỗ ở học kỳ 1.',
                'reviewed_by' => $adminId,
                'reviewed_at' => $now,
                'rejection_reason' => 'Tạm thời chưa còn loại phòng phù hợp.',
                'created_at' => $now,
                'updated_at' => $now,
            ],
        ]);

        // =========================================================
        // 4. BUILDINGS - ROOMS - BEDS - Thành viên 2
        // =========================================================
        $this->call([
            BuildingRoomBedSeeder::class,
        ]);

        // =========================================================
        // 5. ALLOCATIONS - CONTRACTS - VIOLATIONS - Thành viên 3
        // =========================================================
        $this->call([
            Member3Seeder::class,
        ]);
    }
}