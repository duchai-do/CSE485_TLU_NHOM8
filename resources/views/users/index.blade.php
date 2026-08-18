@extends('layouts.app')

@section('title', 'Tài khoản')

@section('content')
<div class="page-header">
    <div>
        <h1>Quản lý tài khoản</h1>
        <p>Quản lý sinh viên, cán bộ và quản trị viên.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('users.create') }}">+ Thêm tài khoản</a>
</div>

<div class="card">
    <form method="GET" class="filters" style="grid-template-columns:2fr 1fr 1fr auto;">
        <div>
            <label>Tìm kiếm</label>
            <input name="search" value="{{ request('search') }}" placeholder="Tên hoặc email">
        </div>
        <div>
            <label>Vai trò</label>
            <select name="role">
                <option value="">Tất cả</option>
                <option value="student" @selected(request('role') === 'student')>Sinh viên</option>
                <option value="staff" @selected(request('role') === 'staff')>Cán bộ</option>
                <option value="admin" @selected(request('role') === 'admin')>Admin</option>
            </select>
        </div>
        <div>
            <label>Trạng thái</label>
            <select name="status">
                <option value="">Tất cả</option>
                <option value="active" @selected(request('status') === 'active')>Hoạt động</option>
                <option value="inactive" @selected(request('status') === 'inactive')>Khóa</option>
            </select>
        </div>
        <div class="actions">
            <button class="btn btn-primary">Lọc</button>
            <a class="btn btn-light" href="{{ route('users.index') }}">Xóa lọc</a>
        </div>
    </form>
</div>

<div class="card table-wrap">
    <table>
        <thead>
        <tr>
            <th>ID</th>
            <th>Họ tên</th>
            <th>Email</th>
            <th>Vai trò</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>
        </thead>
        <tbody>
        @forelse($users as $user)
            <tr>
                <td>{{ $user->id }}</td>
                <td>{{ $user->name }}</td>
                <td>{{ $user->email }}</td>
                <td>{{ $user->role }}</td>
                <td>
                    <span class="badge {{ $user->status ? 'badge-active' : 'badge-inactive' }}">
                        {{ $user->status ? 'Hoạt động' : 'Đã khóa' }}
                    </span>
                </td>
                <td>
                    <div class="actions">
                        <a class="btn btn-warning" href="{{ route('users.edit', $user) }}">Sửa</a>
                        <form method="POST" action="{{ route('users.destroy', $user) }}"
                              onsubmit="return confirm('Xóa tài khoản này?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger">Xóa</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="6">Chưa có dữ liệu.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="pagination">{{ $users->links() }}</div>
</div>
@endsection
