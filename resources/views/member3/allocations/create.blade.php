@extends('layouts.app')

@section('title', 'Xếp giường mới')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header">Xếp giường cho sinh viên</div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('member3.allocations.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Đơn đã duyệt</label>
                        <select name="registration_id" class="form-select" required>
                            <option value="">-- Chọn đơn đăng ký --</option>
                            @foreach($registrations as $registration)
                                <option value="{{ $registration->id }}"
                                    @selected((string) old('registration_id', request('registration_id')) === (string) $registration->id)>
                                    {{ $registration->student?->student_code }}
                                    - {{ $registration->student?->user?->name }}
                                    - {{ $registration->student?->gender }}
                                    - {{ $registration->preferred_room_type }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Giường trống</label>
                        <select name="bed_id" class="form-select" required>
                            <option value="">-- Chọn giường --</option>
                            @foreach($beds as $bed)
                                <option value="{{ $bed->id }}" @selected(old('bed_id') == $bed->id)>
                                    {{ $bed->room?->building?->name }}
                                    / {{ $bed->room?->room_number }}
                                    / {{ $bed->bed_number }}
                                    - {{ $bed->room?->type }}
                                    - {{ $bed->room?->capacity }} người
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Ngày bắt đầu</label>
                            <input type="date" name="start_date" class="form-control"
                                   value="{{ old('start_date', now()->toDateString()) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày kết thúc dự kiến</label>
                            <input type="date" name="end_date" class="form-control"
                                   value="{{ old('end_date') }}">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="note" class="form-control" rows="3">{{ old('note') }}</textarea>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-primary">Lưu xếp giường</button>
                        <a href="{{ route('member3.allocations.index') }}" class="btn btn-outline-secondary">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
