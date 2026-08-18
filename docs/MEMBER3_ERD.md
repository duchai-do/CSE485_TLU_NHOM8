# ERD phần Thành viên 3

```mermaid
erDiagram
    ROOM_REGISTRATIONS ||--|| ALLOCATIONS : "được xếp"
    STUDENTS ||--o{ ALLOCATIONS : "có"
    BEDS ||--o{ ALLOCATIONS : "được sử dụng"
    USERS ||--o{ ALLOCATIONS : "thực hiện xếp"
    ALLOCATIONS ||--|| CONTRACTS : "tạo hợp đồng"
    STUDENTS ||--o{ VIOLATION_RECORDS : "có vi phạm"
    CONTRACTS ||--o{ VIOLATION_RECORDS : "liên quan"
    USERS ||--o{ VIOLATION_RECORDS : "lập biên bản"
```

## Khóa ngoại

- `allocations.registration_id` → `room_registrations.id` (duy nhất).
- `allocations.student_id` → `students.id`.
- `allocations.bed_id` → `beds.id`.
- `allocations.allocated_by` → `users.id`.
- `contracts.allocation_id` → `allocations.id` (duy nhất).
- `violation_records.student_id` → `students.id`.
- `violation_records.contract_id` → `contracts.id` (có thể null).
- `violation_records.recorded_by` → `users.id`.
