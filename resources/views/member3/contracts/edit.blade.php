@extends('layouts.app')

@section('title', 'Sửa hợp đồng')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Sửa hợp đồng {{ $contract->contract_code }}</div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('member3.contracts.update', $contract) }}">
                    @csrf
                    @method('PUT')

                    <div class="mb-3">
                        <label class="form-label">Mã hợp đồng</label>
                        <input name="contract_code" class="form-control"
                               value="{{ old('contract_code', $contract->contract_code) }}" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Ngày bắt đầu</label>
                            <input type="date" name="start_date" class="form-control"
                                   value="{{ old('start_date', $contract->start_date?->toDateString()) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày kết thúc</label>
                            <input type="date" name="end_date" class="form-control"
                                   value="{{ old('end_date', $contract->end_date?->toDateString()) }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mt-0">
                        <div class="col-md-6">
                            <label class="form-label">Giá mỗi tháng</label>
                            <input type="number" min="0" step="1000" name="monthly_price" class="form-control"
                                   value="{{ old('monthly_price', $contract->monthly_price) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tiền đặt cọc</label>
                            <input type="number" min="0" step="1000" name="deposit_amount" class="form-control"
                                   value="{{ old('deposit_amount', $contract->deposit_amount) }}" required>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-primary">Cập nhật</button>
                        <a href="{{ route('member3.contracts.index') }}" class="btn btn-outline-secondary">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection
