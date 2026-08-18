@extends('layouts.app')

@section('title', 'Sửa biên bản vi phạm')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Sửa biên bản #{{ $violation->id }}</div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('member3.violations.update', $violation) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Sinh viên</label>
                        <select name="student_id" class="form-select" required>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}"
                                    @selected((string) old('student_id', $violation->student_id) === (string) $student->id)>
                                    {{ $student->student_code }} - {{ $student->user?->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Hợp đồng liên quan</label>
                        <select name="contract_id" class="form-select">
                            <option value="">-- Không gắn hợp đồng --</option>
                            @foreach($contracts as $contract)
                                <option value="{{ $contract->id }}"
                                    @selected((string) old('contract_id', $violation->contract_id) === (string) $contract->id)>
                                    {{ $contract->contract_code }}
                                    - {{ $contract->allocation?->student?->student_code }}
                                    - {{ $contract->allocation?->student?->user?->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Ngày vi phạm</label>
                            <input type="date" name="violation_date" class="form-control"
                                   value="{{ old('violation_date', $violation->violation_date?->toDateString()) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Loại vi phạm</label>
                            <input name="violation_type" class="form-control"
                                   value="{{ old('violation_type', $violation->violation_type) }}" required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Nội dung</label>
                        <textarea name="description" class="form-control" rows="4" required>{{ old('description', $violation->description) }}</textarea>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Mức phạt</label>
                        <input type="number" min="0" step="1000" name="penalty_amount"
                               class="form-control" value="{{ old('penalty_amount', $violation->penalty_amount) }}" required>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-primary">Cập nhật</button>
                        <a href="{{ route('member3.violations.index') }}" class="btn btn-outline-secondary">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
