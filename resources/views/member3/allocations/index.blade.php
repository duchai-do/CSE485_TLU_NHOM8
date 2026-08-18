@extends('layouts.app')

@section('title', 'Xếp giường')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Danh sách xếp giường</h2>
        <div class="text-muted">CRUD/workflow Thành viên 3</div>
    </div>
    <a href="{{ route('member3.allocations.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Xếp giường mới
    </a>
</div>

<form class="row g-2 mb-3">
    <div class="col-auto">
        <select name="status" class="form-select">
            <option value="">Tất cả trạng thái</option>
            @foreach(['active', 'ended', 'cancelled'] as $status)
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
                    <th>Sinh viên</th>
                    <th>Vị trí</th>
                    <th>Thời gian</th>
                    <th>Trạng thái</th>
                    <th>Người xếp</th>
                    <th>Hợp đồng</th>
                    <th class="text-end">Thao tác</th>
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
                        <td>
                            <span class="badge text-bg-{{ $allocation->status === 'active' ? 'success' : 'secondary' }}">
                                {{ $allocation->status }}
                            </span>
                        </td>
                        <td>{{ $allocation->allocator?->name }}</td>
                        <td>{{ $allocation->contract?->contract_code ?: 'Chưa có' }}</td>
                        <td class="text-end">
                            @if($allocation->status === 'active')
                                <a href="{{ route('member3.allocations.edit', $allocation) }}" class="btn btn-sm btn-outline-primary">
                                    Sửa
                                </a>

                                @if(!$allocation->contract)
                                    <form method="POST" action="{{ route('member3.allocations.cancel', $allocation) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-outline-danger"
                                                onclick="return confirm('Hủy allocation này?')">
                                            Hủy
                                        </button>
                                    </form>
                                @elseif($allocation->contract->status !== 'active')
                                    <form method="POST" action="{{ route('member3.allocations.end', $allocation) }}" class="d-inline">
                                        @csrf
                                        @method('PATCH')
                                        <button class="btn btn-sm btn-outline-secondary"
                                                onclick="return confirm('Kết thúc allocation này?')">
                                            Kết thúc
                                        </button>
                                    </form>
                                @endif
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">Chưa có dữ liệu xếp giường.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
