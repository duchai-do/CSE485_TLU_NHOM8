@extends('layouts.app')
@section('content')
<div class="actions"><h1>Hóa đơn {{ $invoice->invoice_code }}</h1><a href="{{ route('invoices.print',$invoice) }}" target="_blank">In hóa đơn</a></div>
<div class="card"><p>Khách hàng: {{ $invoice->contract->allocation->student->user->name ?? '—' }}</p><p>Tháng: {{ $invoice->billing_month }}/{{ $invoice->billing_year }} · Hạn: {{ $invoice->due_date?->format('d/m/Y') ?? '—' }}</p><p>Trạng thái: {{ $invoice->status }}</p>@if($invoice->status !== 'paid')<form method="post" action="{{ route('invoices.paid',$invoice) }}">@csrf @method('PATCH')<button>Xác nhận đã thanh toán</button></form>@endif</div>
<table><tr><th>Loại</th><th>Mô tả</th><th>SL</th><th>Đơn giá</th><th>Thành tiền</th></tr>@foreach($invoice->items as $item)<tr><td>{{ $item->item_type }}</td><td>{{ $item->description }}</td><td>{{ $item->quantity }}</td><td>{{ number_format($item->unit_price) }} đ</td><td>{{ number_format($item->amount) }} đ</td></tr>@endforeach<tr><th colspan="4" class="right">Tổng cộng</th><th>{{ number_format($invoice->total_amount) }} đ</th></tr></table>
@endsection
