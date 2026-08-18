@extends('layouts.app')

@section('title', 'Lập biên bản vi phạm')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Lập biên bản vi phạm</div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('member3.violations.store') }}">
                    @csrf

                    <div class="mb-3">
                        <label class="form-label">Sinh viên</label>
                        <select name="student_id" class="form-select" required>
                            <option value="">-- Chọn sinh viên --</option>
                            @foreach($students as $student)
                                <option value="{{ $student->id }}" @selected(old('student_id') == $student->id)>
                                    {{ $student->student_code }} - {{ $student->user?->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Hợp đồng liên quan (không bắt buộc)</label>
                        <select name="contract_id" class="form-select">
                            <option value="">-- Không gắn hợp đồng --</option>
                            @foreach($contracts as $contract)
                                <option value="{{ $contract->id }}" @selected(old('contract_id') == $contract->id)>
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
                                   value="{{ old('violation_date', now()->toDateString()) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Loại vi phạm</label>
                            <input name="violation_type" class="form-control"
                                   value="{{ old('violation_type') }}" required>
                        </div>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Nội dung</label>
                        <textarea name="description" class="form-control" rows="4" required>{{ old('description') }}</textarea>
                    </div>

                    <div class="mt-3">
                        <label class="form-label">Mức phạt</label>
                        <input type="number" min="0" step="1000" name="penalty_amount"
                               class="form-control" value="{{ old('penalty_amount', 0) }}" required>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-primary">Lưu biên bản</button>
                        <a href="{{ route('member3.violations.index') }}" class="btn btn-outline-secondary">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
