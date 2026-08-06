@extends('layouts.app')
@section('content')
<h1>Nhập chỉ số điện nước</h1><form method="post" action="{{ route('utility-readings.store') }}">@csrf
<p><label>Phòng <select name="room_id" required><option value="">-- Chọn phòng --</option>@foreach($rooms as $room)<option value="{{ $room->id }}" @selected(old('room_id') == $room->id)>{{ $room->building->code }} - {{ $room->room_number }}</option>@endforeach</select></label></p>
<p><label>Tháng <input name="reading_month" type="number" min="1" max="12" value="{{ old('reading_month', now()->month) }}" required></label><label>Năm <input name="reading_year" type="number" min="2000" value="{{ old('reading_year', now()->year) }}" required></label></p>
<p><label>Điện cũ <input name="previous_electricity" type="number" step="0.01" min="0" value="{{ old('previous_electricity', 0) }}" required></label><label>Điện mới <input name="current_electricity" type="number" step="0.01" min="0" value="{{ old('current_electricity') }}" required></label><label>Đơn giá điện <input name="electricity_unit_price" type="number" step="0.01" min="0" value="{{ old('electricity_unit_price') }}" required></label></p>
<p><label>Nước cũ <input name="previous_water" type="number" step="0.01" min="0" value="{{ old('previous_water', 0) }}" required></label><label>Nước mới <input name="current_water" type="number" step="0.01" min="0" value="{{ old('current_water') }}" required></label><label>Đơn giá nước <input name="water_unit_price" type="number" step="0.01" min="0" value="{{ old('water_unit_price') }}" required></label></p>
<button>Lưu chỉ số</button></form>
@endsection
