@extends('layouts.app')
@section('content')
<h1>Thống kê doanh thu</h1><form method="get"><label>Năm <input type="number" name="year" min="2000" value="{{ $year }}"></label><button>Xem</button></form>
<div class="card"><strong>Hóa đơn chưa thanh toán / quá hạn: {{ number_format($unpaidTotal) }} đ</strong></div>
<table><tr><th>Tháng</th><th>Doanh thu đã thu</th></tr>@forelse($revenue as $row)<tr><td>{{ $row->billing_month }}/{{ $year }}</td><td>{{ number_format($row->total) }} đ</td></tr>@empty<tr><td colspan="2">Chưa có hóa đơn đã thanh toán trong năm này.</td></tr>@endforelse<tr><th>Tổng doanh thu</th><th>{{ number_format($revenue->sum('total')) }} đ</th></tr></table>
@endsection
