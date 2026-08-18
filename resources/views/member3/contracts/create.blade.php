@extends('layouts.app')

@section('title', 'Lập hợp đồng')

@section('content')
<div class="row justify-content-center">
    <div class="col-lg-8">
        <div class="card">
            <div class="card-header">Lập hợp đồng từ allocation</div>
            <div class="card-body">
                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('member3.contracts.store') }}">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Allocation chưa có hợp đồng</label>
                        <select name="allocation_id" id="allocation_id" class="form-select" required>
                            <option value="">-- Chọn allocation --</option>
                            @foreach($allocations as $allocation)
                                <option value="{{ $allocation->id }}"
                                        data-price="{{ $allocation->bed?->room?->price }}"
                                        @selected(old('allocation_id') == $allocation->id)>
                                    {{ $allocation->student?->student_code }}
                                    - {{ $allocation->student?->user?->name }}
                                    - {{ $allocation->bed?->room?->room_number }}/{{ $allocation->bed?->bed_number }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Mã hợp đồng</label>
                        <input name="contract_code" class="form-control"
                               value="{{ old('contract_code', 'HD-' . now()->format('YmdHis')) }}" required>
                    </div>

                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">Ngày bắt đầu</label>
                            <input type="date" name="start_date" class="form-control"
                                   value="{{ old('start_date', now()->toDateString()) }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Ngày kết thúc</label>
                            <input type="date" name="end_date" class="form-control"
                                   value="{{ old('end_date', now()->addMonths(5)->toDateString()) }}" required>
                        </div>
                    </div>

                    <div class="row g-3 mt-0">
                        <div class="col-md-6">
                            <label class="form-label">Giá mỗi tháng</label>
                            <input type="number" min="0" step="1000" name="monthly_price" id="monthly_price"
                                   class="form-control" value="{{ old('monthly_price') }}" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Tiền đặt cọc</label>
                            <input type="number" min="0" step="1000" name="deposit_amount"
                                   class="form-control" value="{{ old('deposit_amount', 500000) }}" required>
                        </div>
                    </div>

                    <div class="mt-4 d-flex gap-2">
                        <button class="btn btn-primary">Lập hợp đồng</button>
                        <a href="{{ route('member3.contracts.index') }}" class="btn btn-outline-secondary">Quay lại</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</div>

<script>
document.getElementById('allocation_id')?.addEventListener('change', function () {
    const option = this.options[this.selectedIndex];
    const price = option?.dataset?.price;
    if (price) document.getElementById('monthly_price').value = price;
});
</script>
@endsection
