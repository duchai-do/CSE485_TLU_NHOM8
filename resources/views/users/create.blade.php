@extends('layouts.app')

@section('title', 'Thêm tài khoản')

@section('content')
<div class="page-header">
    <div>
        <h1>Thêm tài khoản</h1>
        <p>Tạo tài khoản mới cho hệ thống.</p>
    </div>
</div>

<form method="POST" action="{{ route('users.store') }}">
    @csrf
    <div class="card">
        <div class="form-grid">
            <div class="form-group">
                <label>Họ tên *</label>
                <input name="name" value="{{ old('name') }}" required>
            </div>
            <div class="form-group">
                <label>Email *</label>
                <input type="email" name="email" value="{{ old('email') }}" required>
            </div>
            <div class="form-group">
                <label>Mật khẩu *</label>
                <input type="password" name="password" required>
            </div>
            <div class="form-group">
                <label>Nhập lại mật khẩu *</label>
                <input type="password" name="password_confirmation" required>
            </div>
            <div class="form-group">
                <label>Vai trò *</label>
                <select name="role" required>
                    <option value="student" @selected(old('role', 'student') === 'student')>Sinh viên</option>
                    <option value="staff" @selected(old('role') === 'staff')>Cán bộ</option>
                    <option value="admin" @selected(old('role') === 'admin')>Admin</option>
                </select>
            </div>
            <div class="form-group">
                <label>Trạng thái *</label>
                <select name="status" required>
                    <option value="1" @selected(old('status', '1') === '1')>Hoạt động</option>
                    <option value="0" @selected(old('status') === '0')>Khóa</option>
                </select>
            </div>
        </div>
        <div class="actions">
            <button class="btn btn-primary">Lưu tài khoản</button>
            <a class="btn btn-light" href="{{ route('users.index') }}">Quay lại</a>
        </div>
    </div>
</form>
@endsection
