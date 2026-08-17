@extends('layouts.app')

@section('title', 'Hợp đồng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Danh sách hợp đồng</h2>
    <a href="{{ route('member3.contracts.create') }}" class="btn btn-primary">Lập hợp đồng</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>Mã HĐ</th>
                    <th>Sinh viên</th>
                    <th>Phòng / giường</th>
                    <th>Thời hạn</th>
                    <th>Giá tháng</th>
                    <th>Đặt cọc</th>
                    <th>Trạng thái</th>
                </tr>
                </thead>
                <tbody>
                @forelse($contracts as $contract)
                    <tr>
                        <td><strong>{{ $contract->contract_code }}</strong></td>
                        <td>
                            {{ $contract->allocation?->student?->student_code }}<br>
                            <span class="text-muted">{{ $contract->allocation?->student?->user?->name }}</span>
                        </td>
                        <td>
                            {{ $contract->allocation?->bed?->room?->room_number }}
                            / {{ $contract->allocation?->bed?->bed_number }}
                        </td>
                        <td>{{ $contract->start_date?->format('d/m/Y') }} → {{ $contract->end_date?->format('d/m/Y') }}</td>
                        <td>{{ number_format((float) $contract->monthly_price, 0, ',', '.') }} đ</td>
                        <td>{{ number_format((float) $contract->deposit_amount, 0, ',', '.') }} đ</td>
                        <td><span class="badge text-bg-success">{{ $contract->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">Chưa có hợp đồng.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
