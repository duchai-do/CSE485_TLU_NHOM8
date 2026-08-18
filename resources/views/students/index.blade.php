@extends('layouts.app')

@section('title', 'Hồ sơ sinh viên')

@section('content')
<div class="page-header">
    <div>
        <h1>Hồ sơ sinh viên</h1>
        <p>Tìm kiếm theo mã sinh viên, họ tên, email hoặc lớp.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('students.create') }}">+ Thêm hồ sơ</a>
</div>

<div class="card">
    <form method="GET" class="filters" style="grid-template-columns:2fr 1fr auto;">
        <div>
            <label>Tìm kiếm</label>
            <input name="search" value="{{ request('search') }}"
                   placeholder="Mã SV, họ tên, email, lớp...">
        </div>
        <div>
            <label>Khoa</label>
            <select name="faculty">
                <option value="">Tất cả khoa</option>
                @foreach($faculties as $faculty)
                    <option value="{{ $faculty }}" @selected(request('faculty') === $faculty)>
                        {{ $faculty }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="actions">
            <button class="btn btn-primary">Tìm</button>
            <a class="btn btn-light" href="{{ route('students.index') }}">Xóa lọc</a>
        </div>
    </form>
</div>

<div class="card table-wrap">
    <table>
        <thead>
        <tr>
            <th>Mã SV</th>
            <th>Họ tên</th>
            <th>Lớp</th>
            <th>Khoa</th>
            <th>Điện thoại</th>
            <th>Ưu tiên</th>
            <th>Thao tác</th>
        </tr>
        </thead>
        <tbody>
        @forelse($students as $student)
            <tr>
                <td><strong>{{ $student->student_code }}</strong></td>
                <td>{{ $student->user?->name }}</td>
                <td>{{ $student->class_name ?: '-' }}</td>
                <td>{{ $student->faculty ?: '-' }}</td>
                <td>{{ $student->phone ?: '-' }}</td>
                <td>{{ $student->priority_type ?: 'Không' }}</td>
                <td>
                    <div class="actions">
                        <a class="btn btn-light" href="{{ route('students.show', $student) }}">Xem</a>
                        <a class="btn btn-warning" href="{{ route('students.edit', $student) }}">Sửa</a>
                        <form method="POST" action="{{ route('students.destroy', $student) }}"
                              onsubmit="return confirm('Xóa hồ sơ sinh viên này?')">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-danger">Xóa</button>
                        </form>
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="7">Không tìm thấy sinh viên.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="pagination">{{ $students->links() }}</div>
</div>
@endsection
