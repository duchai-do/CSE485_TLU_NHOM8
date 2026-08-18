# Data Dictionary – Thành viên 3

## 1. Bảng `allocations`

| Cột | Kiểu | Null | Ràng buộc/Mặc định | Ý nghĩa |
|---|---|---:|---|---|
| id | bigint | Không | PK, tự tăng | Mã xếp giường |
| registration_id | bigint | Không | FK, unique | Đơn đăng ký được xét |
| student_id | bigint | Không | FK | Sinh viên được xếp |
| bed_id | bigint | Không | FK | Giường được xếp |
| allocated_by | bigint | Không | FK | Người thực hiện xếp |
| start_date | date | Không |  | Ngày bắt đầu ở |
| end_date | date | Có |  | Ngày kết thúc dự kiến |
| status | varchar(30) | Không | `active` | Trạng thái xếp giường |
| note | text | Có |  | Ghi chú |
| created_at | timestamp | Có | Laravel timestamps | Ngày tạo |
| updated_at | timestamp | Có | Laravel timestamps | Ngày cập nhật |

## 2. Bảng `contracts`

| Cột | Kiểu | Null | Ràng buộc/Mặc định | Ý nghĩa |
|---|---|---:|---|---|
| id | bigint | Không | PK, tự tăng | Mã hợp đồng |
| allocation_id | bigint | Không | FK, unique | Xếp giường tương ứng |
| contract_code | varchar(50) | Không | unique | Mã hợp đồng |
| start_date | date | Không |  | Ngày bắt đầu |
| end_date | date | Không |  | Ngày kết thúc |
| monthly_price | decimal(12,2) | Không |  | Tiền phòng mỗi tháng |
| deposit_amount | decimal(12,2) | Không | `0` | Tiền đặt cọc |
| status | varchar(30) | Không | `active` | Trạng thái hợp đồng |
| signed_at | datetime | Có |  | Thời điểm ký |
| terminated_at | datetime | Có |  | Thời điểm chấm dứt |
| termination_reason | text | Có |  | Lý do chấm dứt |
| created_at | timestamp | Có | Laravel timestamps | Ngày tạo |
| updated_at | timestamp | Có | Laravel timestamps | Ngày cập nhật |

## 3. Bảng `violation_records`

| Cột | Kiểu | Null | Ràng buộc/Mặc định | Ý nghĩa |
|---|---|---:|---|---|
| id | bigint | Không | PK, tự tăng | Mã biên bản |
| student_id | bigint | Không | FK | Sinh viên vi phạm |
| contract_id | bigint | Có | FK | Hợp đồng liên quan |
| recorded_by | bigint | Không | FK | Người lập biên bản |
| violation_date | date | Không |  | Ngày vi phạm |
| violation_type | varchar(100) | Không |  | Loại vi phạm |
| description | text | Không |  | Nội dung vi phạm |
| penalty_amount | decimal(12,2) | Không | `0` | Mức phạt |
| status | varchar(30) | Không | `pending` | Trạng thái xử lý |
| resolved_at | datetime | Có |  | Thời điểm xử lý xong |
| created_at | timestamp | Có | Laravel timestamps | Ngày tạo |
| updated_at | timestamp | Có | Laravel timestamps | Ngày cập nhật |
