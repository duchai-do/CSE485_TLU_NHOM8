@extends('layouts.app')

@section('title', 'Đăng ký chỗ ở')

@section('content')
<div class="page-header">
    <div>
        <h1>Đơn đăng ký chỗ ở</h1>
        <p>Tìm kiếm, lọc trạng thái và xét duyệt đơn đăng ký KTX.</p>
    </div>
    <a class="btn btn-primary" href="{{ route('room-registrations.create') }}">+ Tạo đơn</a>
</div>

<div class="card">
    <form method="GET" class="filters">
        <div>
            <label>Tìm sinh viên</label>
            <input name="search" value="{{ request('search') }}"
                   placeholder="Mã SV hoặc họ tên">
        </div>
        <div>
            <label>Trạng thái</label>
            <select name="status">
                <option value="">Tất cả</option>
                <option value="pending" @selected(request('status') === 'pending')>Chờ duyệt</option>
                <option value="approved" @selected(request('status') === 'approved')>Đã duyệt</option>
                <option value="rejected" @selected(request('status') === 'rejected')>Từ chối</option>
            </select>
        </div>
        <div>
            <label>Học kỳ</label>
            <select name="semester">
                <option value="">Tất cả</option>
                <option value="1" @selected(request('semester') === '1')>Học kỳ 1</option>
                <option value="2" @selected(request('semester') === '2')>Học kỳ 2</option>
                <option value="Hè" @selected(request('semester') === 'Hè')>Hè</option>
            </select>
        </div>
        <div>
            <label>Năm học</label>
            <select name="academic_year">
                <option value="">Tất cả</option>
                @foreach($academicYears as $year)
                    <option value="{{ $year }}" @selected(request('academic_year') === $year)>
                        {{ $year }}
                    </option>
                @endforeach
            </select>
        </div>
        <div class="actions">
            <button class="btn btn-primary">Lọc</button>
            <a class="btn btn-light" href="{{ route('room-registrations.index') }}">Xóa lọc</a>
        </div>
    </form>
</div>

<div class="card table-wrap">
    <table>
        <thead>
        <tr>
            <th>Sinh viên</th>
            <th>Học kỳ</th>
            <th>Năm học</th>
            <th>Loại phòng</th>
            <th>Điểm ưu tiên</th>
            <th>Trạng thái</th>
            <th>Thao tác</th>
        </tr>
        </thead>
        <tbody>
        @forelse($registrations as $registration)
            <tr>
                <td>
                    <strong>{{ $registration->student?->student_code }}</strong><br>
                    <span class="muted">{{ $registration->student?->user?->name }}</span>
                </td>
                <td>{{ $registration->semester }}</td>
                <td>{{ $registration->academic_year }}</td>
                <td>{{ $registration->preferred_room_type ?: '-' }}</td>
                <td>{{ $registration->priority_score }}</td>
                <td>
                    <span class="badge badge-{{ $registration->status }}">
                        @if($registration->status === 'pending') Chờ duyệt
                        @elseif($registration->status === 'approved') Đã duyệt
                        @else Từ chối
                        @endif
                    </span>
                </td>
                <td>
                    <div class="actions">
                        <a class="btn btn-light"
                           href="{{ route('room-registrations.show', $registration) }}">
                            Xem
                        </a>
                        @if($registration->status === 'pending')
                            <a class="btn btn-warning"
                               href="{{ route('room-registrations.edit', $registration) }}">
                                Sửa
                            </a>
                            <form method="POST"
                                  action="{{ route('room-registrations.destroy', $registration) }}"
                                  onsubmit="return confirm('Hủy đơn đăng ký này?')">
                                @csrf
                                @method('DELETE')
                                <button class="btn btn-danger">Hủy</button>
                            </form>
                        @endif
                    </div>
                </td>
            </tr>
        @empty
            <tr><td colspan="7">Không có đơn đăng ký phù hợp.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div class="pagination">{{ $registrations->links() }}</div>
</div>
@endsection
