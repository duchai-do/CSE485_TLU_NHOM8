@extends('layouts.app')
@section('content')
<div class="actions"><h1>Chỉ số điện nước</h1><a href="{{ route('utility-readings.create') }}">+ Nhập chỉ số</a></div>
<form method="get"><input type="number" name="month" min="1" max="12" placeholder="Tháng" value="{{ request('month') }}"><input type="number" name="year" min="2000" placeholder="Năm" value="{{ request('year') }}"><button>Lọc</button></form>
<table><tr><th>Phòng</th><th>Tháng</th><th>Điện (cũ → mới)</th><th>Nước (cũ → mới)</th><th>Đơn giá</th></tr>@forelse($readings as $reading)<tr><td>{{ $reading->room->building->code ?? '' }}-{{ $reading->room->room_number }}</td><td>{{ $reading->reading_month }}/{{ $reading->reading_year }}</td><td>{{ $reading->previous_electricity }} → {{ $reading->current_electricity }}</td><td>{{ $reading->previous_water }} → {{ $reading->current_water }}</td><td>Điện: {{ number_format($reading->electricity_unit_price) }}; Nước: {{ number_format($reading->water_unit_price) }}</td></tr>@empty<tr><td colspan="5">Chưa có dữ liệu.</td></tr>@endforelse</table>{{ $readings->links() }}
@endsection
