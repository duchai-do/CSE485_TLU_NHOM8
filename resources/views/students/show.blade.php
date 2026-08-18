@extends('layouts.app')

@section('title', 'Chi tiết sinh viên')

@section('content')
<div class="page-header">
    <div>
        <h1>{{ $student->user?->name }}</h1>
        <p>Mã sinh viên: {{ $student->student_code }}</p>
    </div>
    <div class="actions">
        <a class="btn btn-warning" href="{{ route('students.edit', $student) }}">Sửa hồ sơ</a>
        <a class="btn btn-success"
           href="{{ route('room-registrations.create', ['student_id' => $student->id]) }}">
            + Tạo đơn đăng ký
        </a>
    </div>
</div>

<div class="card">
    <h3>Thông tin sinh viên</h3>
    <div class="detail-grid">
        <div class="key">Email</div><div>{{ $student->user?->email }}</div>
        <div class="key">Ngày sinh</div><div>{{ $student->date_of_birth?->format('d/m/Y') ?: '-' }}</div>
        <div class="key">Giới tính</div><div>{{ $student->gender }}</div>
        <div class="key">Lớp</div><div>{{ $student->class_name ?: '-' }}</div>
        <div class="key">Khoa</div><div>{{ $student->faculty ?: '-' }}</div>
        <div class="key">Điện thoại</div><div>{{ $student->phone ?: '-' }}</div>
        <div class="key">Địa chỉ</div><div>{{ $student->address ?: '-' }}</div>
        <div class="key">Đối tượng ưu tiên</div><div>{{ $student->priority_type ?: 'Không' }}</div>
    </div>
</div>

<div class="card table-wrap">
    <h3>Lịch sử đăng ký chỗ ở</h3>
    <table>
        <thead>
        <tr>
            <th>Học kỳ</th>
            <th>Năm học</th>
            <th>Loại phòng</th>
            <th>Trạng thái</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse($student->roomRegistrations as $registration)
            <tr>
                <td>{{ $registration->semester }}</td>
                <td>{{ $registration->academic_year }}</td>
                <td>{{ $registration->preferred_room_type ?: '-' }}</td>
                <td>
                    <span class="badge badge-{{ $registration->status }}">
                        {{ $registration->status }}
                    </span>
                </td>
                <td>
                    <a class="btn btn-light"
                       href="{{ route('room-registrations.show', $registration) }}">
                        Xem
                    </a>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">Sinh viên chưa có đơn đăng ký.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
