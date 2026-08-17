@extends('layouts.app')

@section('title', 'Vi phạm')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Lịch sử vi phạm</h2>
    <a href="{{ route('member3.violations.create') }}" class="btn btn-primary">Lập biên bản</a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>Ngày</th>
                    <th>Sinh viên</th>
                    <th>Loại vi phạm</th>
                    <th>Mức phạt</th>
                    <th>Hợp đồng</th>
                    <th>Người lập</th>
                    <th>Trạng thái</th>
                </tr>
                </thead>
                <tbody>
                @forelse($violations as $violation)
                    <tr>
                        <td>{{ $violation->violation_date?->format('d/m/Y') }}</td>
                        <td>
                            {{ $violation->student?->student_code }}<br>
                            <span class="text-muted">{{ $violation->student?->user?->name }}</span>
                        </td>
                        <td>{{ $violation->violation_type }}</td>
                        <td>{{ number_format((float) $violation->penalty_amount, 0, ',', '.') }} đ</td>
                        <td>{{ $violation->contract?->contract_code ?: '—' }}</td>
                        <td>{{ $violation->recorder?->name }}</td>
                        <td><span class="badge text-bg-warning">{{ $violation->status }}</span></td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">Chưa có vi phạm.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
