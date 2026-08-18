# NHIỆM VỤ NHÓM TRƯỞNG - PHẦN CODE HOÀN THIỆN

## Module phụ trách
Sinh viên và đăng ký chỗ ở.

## Bảng phụ trách
- `users`
- `students`
- `room_registrations`

## Chức năng đã có trong bộ code
- Quản lý tài khoản người dùng.
- Quản lý hồ sơ sinh viên.
- Tìm kiếm sinh viên theo mã/họ tên/email/lớp.
- Sinh viên gửi đơn đăng ký chỗ ở.
- Sinh viên sửa/hủy đơn đang chờ duyệt.
- Sinh viên xem trạng thái đơn của chính mình.
- Cán bộ/Admin xem danh sách đơn.
- Lọc đơn theo học kỳ, năm học, trạng thái.
- Duyệt/từ chối đơn.
- Phân quyền theo vai trò.
- Seeder dữ liệu demo.
- README hướng dẫn chạy và demo.

## Relationship
- User `hasOne` Student.
- Student `belongsTo` User.
- Student `hasMany` RoomRegistration.
- RoomRegistration `belongsTo` Student.
- RoomRegistration `belongsTo` User qua `reviewed_by`.
- User `hasMany` RoomRegistration qua `reviewed_by`.

## Việc nhóm trưởng vẫn phải làm trực tiếp trên GitHub
Các việc này không thể hoàn thành chỉ bằng file code:
1. Review Pull Request của các thành viên.
2. Chạy thử từng nhánh.
3. Xử lý conflict khi tích hợp.
4. Merge code đạt yêu cầu vào `main`.
5. Kiểm tra commit history từng thành viên.

## Lệnh kiểm tra
```powershell
git switch feature/student-registration
php artisan migrate:fresh --seed
php artisan optimize:clear
php artisan route:list
php artisan serve
```
