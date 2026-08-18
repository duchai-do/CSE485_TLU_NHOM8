@extends('layouts.app')

@section('title', 'Sửa tài khoản')

@section('content')
<div class="page-header">
    <div>
        <h1>Sửa tài khoản</h1>
        <p>{{ $user->name }} — {{ $user->email }}</p>
    </div>
</div>

<form method="POST" action="{{ route('users.update', $user) }}">
    @csrf
    @method('PUT')
    <div class="card">
        <div class="form-grid">
            <div class="form-group">
                <label>Họ tên *</label>
                <input name="name" value="{{ old('name', $user->name) }}" required>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" value="{{ old('email', $user->email) }}" required>
            </div>
            <div class="form-group">
                <label>Mật khẩu mới</label>
                <input type="password" name="password" placeholder="Để trống nếu không đổi">
            </div>
            <div class="form-group">
                <label>Nhập lại mật khẩu mới</label>
                <input type="password" name="password_confirmation">
            </div>
            <div class="form-group">
                <label>Vai trò *</label>
                <select name="role" required>
                    <option value="student" @selected(old('role', $user->role) === 'student')>Sinh viên</option>
                    <option value="staff" @selected(old('role', $user->role) === 'staff')>Cán bộ</option>
                    <option value="admin" @selected(old('role', $user->role) === 'admin')>Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label>Trạng thái *</label>
                <select name="status" required>
                    <option value="1" @selected((string) old('status', (int) $user->status) === '1')>Hoạt động</option>
                    <option value="0" @selected((string) old('status', (int) $user->status) === '0')>Khóa</option>
                </select>
            </div>
        </div>
        <div class="actions">
            <button class="btn btn-primary">Cập nhật</button>
            <a class="btn btn-light" href="{{ route('users.index') }}">Quay lại</a>
        </div>
    </div>
</form>
@endsection
