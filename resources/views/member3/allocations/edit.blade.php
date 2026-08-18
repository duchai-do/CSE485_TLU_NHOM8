@extends('layouts.app')

@section('title', 'Sửa xếp giường')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-9">
        <div class="card">
            <div class="card-header">
                Sửa allocation #{{ $allocation->id }} —
                {{ $allocation->student?->student_code }} {{ $allocation->student?->user?->name }}
            </div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('member3.allocations.update', $allocation) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Giường</label>
                        <select name="bed_id" class="form-select" required>
                            @foreach($beds as $bed)
                                <option value="{{ $bed->id }}"
                                    @selected((string) old('bed_id', $allocation->bed_id) === (string) $bed->id)>
                                    {{ $bed->room?->building?->name }}
                                    / {{ $bed->room?->room_number }}
                                    / {{ $bed->bed_number }}
                                    - {{ $bed->room?->type }}
                                    - {{ $bed->room?->capacity }} người
                                    {{ $bed->id === $allocation->bed_id ? '(hiện tại)' : '' }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Ngày bắt đầu</label>
                            <input type="date" name="start_date" class="form-control"
                                   value="{{ old('start_date', $allocation->start_date?->toDateString()) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày kết thúc</label>
                            <input type="date" name="end_date" class="form-control"
                                   value="{{ old('end_date', $allocation->end_date?->toDateString()) }}">
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Ghi chú</label>
                        <textarea name="note" class="form-control" rows="3">{{ old('note', $allocation->note) }}</textarea>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-primary">Cập nhật</button>
                        <a href="{{ route('member3.allocations.index') }}" class="btn btn-outline-secondary">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
