@extends('layouts.app')

@section('title', 'Vi phạm')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Lịch sử vi phạm</h2>
    <a href="{{ route('member3.violations.create') }}" class="btn btn-primary">Lập biên bản</a>
</div>

<form class="row g-2 mb-3">
    <div class="col-auto">
        <select name="status" class="form-select">
            <option value="">Tất cả trạng thái</option>
            @foreach(['pending', 'resolved'] as $status)
                <option value="{{ $status }}" @selected(request('status') === $status)>{{ $status }}</option>
            @endforeach
        </select>
    </div>
    <div class="col-auto"><button class="btn btn-outline-primary">Lọc</button></div>
</form>

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
                    <th class="text-end">Thao tác</th>
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
                        <td>
                            <strong>{{ $violation->violation_type }}</strong><br>
                            <span class="text-muted">{{ $violation->description }}</span>
                        </td>
                        <td>{{ number_format((float) $violation->penalty_amount, 0, ',', '.') }} đ</td>
                        <td>{{ $violation->contract?->contract_code ?: '—' }}</td>
                        <td>{{ $violation->recorder?->name }}</td>
                        <td>
                            <span class="badge text-bg-{{ $violation->status === 'resolved' ? 'success' : 'warning' }}">
                                {{ $violation->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if($violation->status === 'pending')
                                <a href="{{ route('member3.violations.edit', $violation) }}" class="btn btn-sm btn-outline-primary">Sửa</a>

                                <form method="POST" action="{{ route('member3.violations.resolve', $violation) }}" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-outline-success">Đã xử lý</button>
                                </form>

                                <form method="POST" action="{{ route('member3.violations.destroy', $violation) }}" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger"
                                            onclick="return confirm('Xóa biên bản pending này?')">
                                        Xóa
                                    </button>
                                </form>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8" class="text-center py-4 text-muted">Chưa có vi phạm.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
