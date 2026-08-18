@extends('layouts.app')

@section('title', 'Chi tiết đơn đăng ký')

@section('content')
<div class="page-header">
    <div>
        <h1>Chi tiết đơn đăng ký #{{ $roomRegistration->id }}</h1>
        <p>
            {{ $roomRegistration->student?->student_code }}
            — {{ $roomRegistration->student?->user?->name }}
        </p>
    </div>
    <div class="actions">
        @if($roomRegistration->status === 'pending')
            <a class="btn btn-warning"
               href="{{ route('room-registrations.edit', $roomRegistration) }}">
                Sửa đơn
            </a>
        @endif
        <a class="btn btn-light" href="{{ route('room-registrations.index') }}">Danh sách</a>
    </div>
</div>

<div class="card">
    <div class="detail-grid">
        <div class="key">Sinh viên</div>
        <div>{{ $roomRegistration->student?->student_code }} — {{ $roomRegistration->student?->user?->name }}</div>

        <div class="key">Học kỳ</div>
        <div>{{ $roomRegistration->semester }}</div>

        <div class="key">Năm học</div>
        <div>{{ $roomRegistration->academic_year }}</div>

        <div class="key">Loại phòng</div>
        <div>{{ $roomRegistration->preferred_room_type ?: '-' }}</div>

        <div class="key">Điểm ưu tiên</div>
        <div>{{ $roomRegistration->priority_score }}</div>

        <div class="key">Trạng thái</div>
        <div>
            <span class="badge badge-{{ $roomRegistration->status }}">
                {{ $roomRegistration->status }}
            </span>
        </div>

        <div class="key">Ghi chú</div>
        <div>{{ $roomRegistration->note ?: '-' }}</div>

        <div class="key">Người xét duyệt</div>
        <div>{{ $roomRegistration->reviewer?->name ?: '-' }}</div>

        <div class="key">Thời gian xét</div>
        <div>{{ $roomRegistration->reviewed_at?->format('d/m/Y H:i') ?: '-' }}</div>

        @if($roomRegistration->status === 'rejected')
            <div class="key">Lý do từ chối</div>
            <div>{{ $roomRegistration->rejection_reason }}</div>
        @endif
    </div>
</div>

@if($roomRegistration->status === 'pending' && in_array(auth()->user()->role, ['admin', 'staff']))
<div class="form-grid">
    <div class="card">
        <h3>Duyệt đơn</h3>
        <form method="POST"
              action="{{ route('room-registrations.approve', $roomRegistration) }}">
            @csrf
            <div class="form-group">
                <label>Điểm ưu tiên</label>
                <input type="number" name="priority_score"
                       min="0" max="1000"
                       value="{{ old('priority_score', $roomRegistration->priority_score) }}">
            </div>
            <button class="btn btn-success"
                    onclick="return confirm('Xác nhận duyệt đơn này?')">
                Duyệt đơn
            </button>
        </form>
    </div>

    <div class="card">
        <h3>Từ chối đơn</h3>
        <form method="POST"
              action="{{ route('room-registrations.reject', $roomRegistration) }}">
            @csrf
            <div class="form-group">
                <label>Lý do từ chối *</label>
                <textarea name="rejection_reason" required
                          placeholder="Nhập lý do từ chối">{{ old('rejection_reason') }}</textarea>
            </div>
            <button class="btn btn-danger"
                    onclick="return confirm('Xác nhận từ chối đơn này?')">
                Từ chối
            </button>
        </form>
    </div>
</div>
@endif
@endsection
