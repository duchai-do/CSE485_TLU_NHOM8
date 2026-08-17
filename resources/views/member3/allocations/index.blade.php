@extends('layouts.app')

@section('title', 'Xếp giường')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Danh sách xếp giường</h2>
        <div class="text-muted">Hiển thị dữ liệu quan hệ thay vì chỉ hiển thị ID</div>
    </div>
    <a href="{{ route('member3.allocations.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Xếp giường mới
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>Sinh viên</th>
                    <th>Vị trí</th>
                    <th>Thời gian</th>
                    <th>Trạng thái</th>
                    <th>Người xếp</th>
                    <th>Hợp đồng</th>
                </tr>
                </thead>
                <tbody>
                @forelse($allocations as $allocation)
                    <tr>
                        <td>
                            <strong>{{ $allocation->student?->student_code }}</strong><br>
                            <span class="text-muted">{{ $allocation->student?->user?->name }}</span>
                        </td>
                        <td>
                            {{ $allocation->bed?->room?->building?->name }}<br>
                            <span class="text-muted">
                                {{ $allocation->bed?->room?->room_number }} / {{ $allocation->bed?->bed_number }}
                            </span>
                        </td>
                        <td>
                            {{ $allocation->start_date?->format('d/m/Y') }}
                            → {{ $allocation->end_date?->format('d/m/Y') ?: 'Chưa xác định' }}
                        </td>
                        <td><span class="badge text-bg-primary">{{ $allocation->status }}</span></td>
                        <td>{{ $allocation->allocator?->name }}</td>
                        <td>{{ $allocation->contract?->contract_code ?: 'Chưa có' }}</td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center py-4 text-muted">Chưa có dữ liệu xếp giường.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
