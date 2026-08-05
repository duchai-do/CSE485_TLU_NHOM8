# BẢNG PHÂN CÔNG NHIỆM VỤ

## Đề tài

Hệ thống quản lý ký túc xá và đăng ký chỗ ở

## Thành viên nhóm

| STT | Thành viên | Vai trò | Module phụ trách | Bảng phụ trách | Nhánh Git |
|---:|---|---|---|---|---|
| 1 | Tống Đức Hải | Trưởng nhóm | Hồ sơ sinh viên và đăng ký chỗ ở | users, students, room_registrations | feature/student-registration |
| 2 | Dương Nam Khánh | Thành viên | Tòa nhà, phòng và giường | buildings, rooms, beds | feature/building-room-bed |
| 3 | Nguyễn Công Đồng | Thành viên | Xếp giường, hợp đồng và vi phạm | allocations, contracts, violation_records | feature/allocation-contract |
| 4 | Nguyễn Hải Nam | Thành viên | Điện nước và hóa đơn | utility_readings, invoices, invoice_items | feature/utility-invoice |

---

## 1. Trưởng nhóm – Hồ sơ sinh viên và đăng ký chỗ ở

### Bảng phụ trách

- users
- students
- room_registrations

### Công việc

- Quản lý tài khoản người dùng.
- Quản lý hồ sơ sinh viên.
- Sinh viên gửi đơn đăng ký chỗ ở.
- Sinh viên sửa hoặc hủy đơn đang chờ duyệt.
- Sinh viên xem trạng thái đơn.
- Cán bộ xem danh sách đơn đăng ký.
- Tìm kiếm sinh viên theo mã sinh viên và họ tên.
- Lọc đơn theo học kỳ và trạng thái.
- Thiết kế ERD chung.
- Quản lý nhánh develop.
- Review Pull Request.
- Tổng hợp tài liệu và README.

### Người review

Thành viên 4.

---

## 2. Thành viên 2 – Tòa nhà, phòng và giường

### Bảng phụ trách

- buildings
- rooms
- beds

### Công việc

- CRUD tòa nhà.
- CRUD phòng.
- CRUD giường.
- Quản lý loại phòng và giới tính phòng.
- Quản lý sức chứa.
- Quản lý trạng thái phòng và giường.
- Thống kê số giường trống, đã sử dụng và bảo trì.
- Không cho xóa phòng đang có sinh viên ở.

### Người review

Trưởng nhóm.

---

## 3. Thành viên 3 – Xếp giường, hợp đồng và vi phạm

### Bảng phụ trách

- allocations
- contracts
- violation_records

### Công việc

- Duyệt hoặc từ chối đơn đăng ký.
- Xếp giường cho sinh viên.
- Kiểm tra giường còn trống.
- Không cho một sinh viên sử dụng hai giường cùng lúc.
- Không cho hai sinh viên sử dụng cùng một giường cùng lúc.
- Lập hợp đồng.
- Gia hạn và chấm dứt hợp đồng.
- Ghi nhận vi phạm.
- Khi chấm dứt hợp đồng, trả giường về trạng thái trống.

### Người review

Thành viên 2.

---

## 4. Thành viên 4 – Điện nước và hóa đơn

### Bảng phụ trách

- utility_readings
- invoices
- invoice_items

### Công việc

- Nhập chỉ số điện và nước theo phòng.
- Không cho chỉ số mới nhỏ hơn chỉ số cũ.
- Không cho nhập trùng dữ liệu một phòng trong cùng tháng.
- Tạo hóa đơn.
- Tạo chi tiết các khoản tiền.
- Tính tổng hóa đơn.
- Theo dõi trạng thái thanh toán.
- Lọc hóa đơn theo tháng và trạng thái.
- In hóa đơn.
- Thống kê doanh thu.

### Người review

Thành viên 3.

---

## Quy định chung

- Không code trực tiếp trên main.
- Mỗi người làm trên nhánh riêng.
- Không tự ý sửa migration của thành viên khác.
- Thay đổi khóa ngoại phải thông báo trưởng nhóm.
- Mỗi chức năng phải có validation backend.
- Mỗi người phải có commit và Pull Request riêng.
- Không commit .env, vendor hoặc node_modules.
- Trước khi tạo Pull Request phải chạy được:

php artisan migrate:fresh --seed