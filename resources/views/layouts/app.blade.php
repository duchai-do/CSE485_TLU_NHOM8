<!DOCTYPE html>
<html lang="vi">
<head><meta charset="utf-8"><meta name="viewport" content="width=device-width, initial-scale=1"><title>Quản lý KTX</title><style>body{font-family:Arial,sans-serif;max-width:1100px;margin:30px auto;padding:0 16px;color:#1f2937}nav{display:flex;gap:18px;margin-bottom:24px}a{color:#2563eb;text-decoration:none}table{width:100%;border-collapse:collapse;margin:16px 0}th,td{border:1px solid #d1d5db;padding:9px;text-align:left}th{background:#f3f4f6}input,select,button{padding:8px;margin:4px 0}button{cursor:pointer;background:#2563eb;color:white;border:0;border-radius:4px}.success{padding:10px;background:#dcfce7}.errors{padding:10px;background:#fee2e2}.actions{display:flex;gap:8px;align-items:center}.card{border:1px solid #d1d5db;padding:16px;margin:12px 0;border-radius:6px}.right{text-align:right}</style></head>
<body>
<nav><a href="{{ route('utility-readings.index') }}">Chỉ số điện nước</a><a href="{{ route('invoices.index') }}">Hóa đơn</a><a href="{{ route('invoices.revenue') }}">Doanh thu</a></nav>
@if(session('success'))<p class="success">{{ session('success') }}</p>@endif
@if($errors->any())<div class="errors"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
@yield('content')
</body></html>
