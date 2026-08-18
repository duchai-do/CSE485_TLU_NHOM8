@extends('layouts.app')

@section('title', 'Tổng quan')

@section('content')
<div class="page-header">
    <div>
        <h1>Tổng quan hệ thống</h1>
        <p>
            @if($isStudent)
                Theo dõi trạng thái đăng ký chỗ ở của bạn.
            @else
                Theo dõi nhanh dữ liệu sinh viên và đăng ký ký túc xá.
            @endif
        </p>
    </div>
</div>

<div class="grid">
    @if(!$isStudent)
        <div class="card stat">
            <div class="number">{{ $userCount }}</div>
            <div class="label">Tài khoản</div>
        </div>
        <div class="card stat">
            <div class="number">{{ $studentCount }}</div>
            <div class="label">Hồ sơ sinh viên</div>
        </div>
    @endif

    <div class="card stat">
        <div class="number">{{ $registrationCount }}</div>
        <div class="label">Tổng đơn đăng ký</div>
    </div>
    <div class="card stat">
        <div class="number">{{ $pendingCount }}</div>
        <div class="label">Đang chờ duyệt</div>
    </div>
    <div class="card stat">
        <div class="number">{{ $approvedCount }}</div>
        <div class="label">Đã duyệt</div>
    </div>
    <div class="card stat">
        <div class="number">{{ $rejectedCount }}</div>
        <div class="label">Đã từ chối</div>
    </div>
</div>

<div class="card">
    @if($isStudent)
        <h3 style="margin-top:0">Thao tác nhanh</h3>

        @if(!$student)
            <p class="muted">Tài khoản chưa có hồ sơ sinh viên. Hãy liên hệ cán bộ quản lý.</p>
        @else
            <div class="actions">
                <a class="btn btn-primary" href="{{ route('room-registrations.create') }}">+ Gửi đơn đăng ký chỗ ở</a>
                <a class="btn btn-success" href="{{ route('room-registrations.index') }}">Xem trạng thái đơn</a>
            </div>
        @endif
    @else
        <h3 style="margin-top:0">Luồng quản lý</h3>
        <p class="muted">Tạo tài khoản → tạo hồ sơ sinh viên → sinh viên gửi đơn → cán bộ xem danh sách → duyệt hoặc từ chối → sinh viên xem trạng thái.</p>
        <div class="actions">
            @if(auth()->user()->role === 'admin')
                <a class="btn btn-light" href="{{ route('users.create') }}">+ Tạo tài khoản</a>
            @endif
            <a class="btn btn-primary" href="{{ route('students.create') }}">+ Thêm hồ sơ sinh viên</a>
            <a class="btn btn-success" href="{{ route('room-registrations.index', ['status' => 'pending']) }}">Xem đơn chờ duyệt</a>
        </div>
    @endif
</div>
@endsection
