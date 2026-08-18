@extends('layouts.app')

@section('title', 'Nhập chỉ số điện nước')

@section('content')
<div class="page-header">
    <div>
        <h1>Nhập chỉ số điện nước</h1>
        <p>Nhập chỉ số điện, nước theo từng phòng và kỳ tháng/năm.</p>
    </div>
</div>

<form method="POST" action="{{ route('utility-readings.store') }}">
    @csrf

    <div class="card">
        <div class="form-grid">
            <div class="form-group">
                <label>Phòng *</label>

                <select name="room_id" required>
                    <option value="">-- Chọn phòng --</option>

                    @foreach($rooms as $room)
                        <option
                            value="{{ $room->id }}"
                            @selected(old('room_id') == $room->id)
                        >
                            {{ $room->building?->code ?? '' }}
                            - {{ $room->room_number }}
                            - {{ $room->type === 'male' ? 'Nam' : ($room->type === 'female' ? 'Nữ' : 'Khác') }}
                            - {{ $room->capacity }} người
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="form-group">
                <label>Tháng *</label>

                <input
                    name="reading_month"
                    type="number"
                    min="1"
                    max="12"
                    value="{{ old('reading_month', now()->month) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label>Năm *</label>

                <input
                    name="reading_year"
                    type="number"
                    min="2000"
                    max="2100"
                    value="{{ old('reading_year', now()->year) }}"
                    required
                >
            </div>
        </div>
    </div>

    <div class="card">
        <h3>Điện</h3>

        <div class="form-grid">
            <div class="form-group">
                <label>Chỉ số điện cũ (kWh) *</label>

                <input
                    name="previous_electricity"
                    type="number"
                    step="0.01"
                    min="0"
                    value="{{ old('previous_electricity', 3400) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label>Chỉ số điện mới (kWh) *</label>

                <input
                    name="current_electricity"
                    type="number"
                    step="0.01"
                    min="0"
                    value="{{ old('current_electricity', 3500) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label>Đơn giá điện (đ/kWh) *</label>

                <input
                    name="electricity_unit_price"
                    type="number"
                    step="0.01"
                    min="0"
                    value="{{ old('electricity_unit_price', 4000) }}"
                    required
                >
            </div>
        </div>
    </div>

    <div class="card">
        <h3>Nước</h3>

        <div class="form-grid">
            <div class="form-group">
                <label>Chỉ số nước cũ (m³) *</label>

                <input
                    name="previous_water"
                    type="number"
                    step="0.01"
                    min="0"
                    value="{{ old('previous_water', 20) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label>Chỉ số nước mới (m³) *</label>

                <input
                    name="current_water"
                    type="number"
                    step="0.01"
                    min="0"
                    value="{{ old('current_water', 28) }}"
                    required
                >
            </div>

            <div class="form-group">
                <label>Đơn giá nước (đ/m³) *</label>

                <input
                    name="water_unit_price"
                    type="number"
                    step="0.01"
                    min="0"
                    value="{{ old('water_unit_price', 15000) }}"
                    required
                >
            </div>
        </div>
    </div>

    <div class="card">
        <div class="alert alert-info">
            Hệ thống sẽ dùng các chỉ số này để tự tính hóa đơn:
            <br>
            Điện = (chỉ số mới - chỉ số cũ) × đơn giá điện
            <br>
            Nước = (chỉ số mới - chỉ số cũ) × đơn giá nước
        </div>

        <div class="actions">
            <button class="btn btn-primary" type="submit">
                Lưu chỉ số
            </button>

            <a
                class="btn btn-light"
                href="{{ route('utility-readings.index') }}"
            >
                Quay lại
            </a>
        </div>
    </div>
</form>
@endsection