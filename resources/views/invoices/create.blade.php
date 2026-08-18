@extends('layouts.app')

@section('title', 'Tạo hóa đơn')

@section('content')
<div class="page-header">
    <div>
        <h1>Tạo hóa đơn</h1>
        <p>
            Chọn hợp đồng và kỳ thanh toán.
            Hệ thống sẽ tự lấy tiền phòng + chỉ số điện + chỉ số nước.
        </p>
    </div>
</div>

<form method="POST" action="{{ route('invoices.store') }}">
    @csrf

    <div class="card">
        <div class="form-grid">
            <div class="form-group">
                <label>Hợp đồng *</label>

                <select name="contract_id" required>
                    <option value="">
                        -- Chọn hợp đồng đang hoạt động --
                    </option>

                    @foreach($contracts as $contract)
                        @php
                            $allocation = $contract->allocation;
                            $student = $allocation?->student;
                            $room = $allocation?->bed?->room;
                            $building = $room?->building;
                        @endphp

                        <option
                            value="{{ $contract->id }}"
                            @selected(old('contract_id') == $contract->id)
                        >
                            {{ $contract->contract_code }}
                            - {{ $student?->user?->name ?? 'Không rõ sinh viên' }}
                            - {{ $building?->name ?? '' }}
                            / {{ $room?->room_number ?? '' }}
                        </option>
                    @endforeach
                </select>

                @if($contracts->isEmpty())
                    <small class="muted">
                        Chưa có hợp đồng active. Hãy lập hợp đồng trước.
                    </small>
                @endif
            </div>

            <div class="form-group">
                <label>Tháng *</label>

                <input
                    type="number"
                    name="billing_month"
                    min="1"
                    max="12"
                    value="{{ old('billing_month', now()->month) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label>Năm *</label>

                <input
                    type="number"
                    name="billing_year"
                    min="2000"
                    max="2100"
                    value="{{ old('billing_year', now()->year) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label>Hạn thanh toán</label>

                <input
                    type="date"
                    name="due_date"
                    value="{{ old('due_date', now()->addDays(10)->toDateString()) }}"
                >
            </div>
        </div>
    </div>

    <div class="card">
        <h3>Cách tính hóa đơn</h3>

        <div class="detail-grid">
            <div class="key">Tiền phòng</div>
            <div>
                Lấy tự động từ giá mỗi tháng của hợp đồng.
            </div>

            <div class="key">Tiền điện</div>
            <div>
                (Chỉ số điện mới - chỉ số điện cũ) × đơn giá điện.
            </div>

            <div class="key">Tiền nước</div>
            <div>
                (Chỉ số nước mới - chỉ số nước cũ) × đơn giá nước.
            </div>

            <div class="key">Tổng hóa đơn</div>
            <div>
                Tiền phòng + tiền điện + tiền nước.
            </div>
        </div>

        <div class="alert alert-info mt-3 mb-0">
            Phải nhập chỉ số điện nước đúng phòng, đúng tháng/năm
            trước khi tạo hóa đơn.
        </div>
    </div>

    <div class="actions">
        <button
            class="btn btn-primary"
            type="submit"
            @disabled($contracts->isEmpty())
        >
            Tạo hóa đơn tự động
        </button>

        <a
            class="btn btn-light"
            href="{{ route('invoices.index') }}"
        >
            Quay lại
        </a>
    </div>
</form>
@endsection