@extends('layouts.app')

@section('title', 'Xét đơn đăng ký')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <div>
        <h2 class="mb-1">Xét đơn đăng ký chỗ ở</h2>
        <div class="text-muted">Thành viên 3: duyệt / từ chối trước khi xếp giường</div>
    </div>
    <a href="{{ route('member3.allocations.create') }}" class="btn btn-primary">
        <i class="bi bi-plus-circle me-1"></i> Xếp giường
    </a>
</div>

<div class="card">
    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-light">
                <tr>
                    <th>SV</th>
                    <th>Học kỳ</th>
                    <th>Loại phòng</th>
                    <th>Ưu tiên</th>
                    <th>Trạng thái</th>
                    <th>Người xét</th>
                    <th class="text-end">Thao tác</th>
                </tr>
                </thead>
                <tbody>
                @forelse($registrations as $registration)
                    <tr>
                        <td>
                            <strong>{{ $registration->student?->student_code }}</strong><br>
                            <span class="text-muted">{{ $registration->student?->user?->name }}</span>
                        </td>
                        <td>{{ $registration->semester }} / {{ $registration->academic_year }}</td>
                        <td>{{ $registration->preferred_room_type ?: '—' }}</td>
                        <td>{{ $registration->priority_score }}</td>
                        <td>
                            <span class="badge text-bg-{{ match($registration->status) {
                                'approved' => 'success',
                                'rejected' => 'danger',
                                'allocated' => 'primary',
                                'cancelled' => 'secondary',
                                default => 'warning'
                            } }}">
                                {{ $registration->status }}
                            </span>
                        </td>
                        <td>{{ $registration->reviewer?->name ?: '—' }}</td>
                        <td class="text-end">
                            @if($registration->status === 'pending')
                                <form class="d-inline" method="POST" action="{{ route('member3.registrations.approve', $registration) }}">
                                    @csrf
                                    @method('PATCH')
                                    <button class="btn btn-sm btn-success">Duyệt</button>
                                </form>

                                <button class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#reject-{{ $registration->id }}">
                                    Từ chối
                                </button>
                            @elseif($registration->status === 'approved')
                                <a href="{{ route('member3.allocations.create', ['registration_id' => $registration->id]) }}"
                                   class="btn btn-sm btn-primary">
                                    Xếp giường
                                </a>
                            @elseif($registration->allocation)
                                <span class="text-muted">
                                    {{ $registration->allocation->bed?->room?->room_number }}
                                    / {{ $registration->allocation->bed?->bed_number }}
                                </span>
                            @endif
                        </td>
                    </tr>
                    @if($registration->status === 'pending')
                        <tr class="collapse" id="reject-{{ $registration->id }}">
                            <td colspan="7" class="bg-light">
                                <form method="POST" action="{{ route('member3.registrations.reject', $registration) }}" class="row g-2">
                                    @csrf
                                    @method('PATCH')
                                    <div class="col">
                                        <input name="rejection_reason"
                                               class="form-control"
                                               placeholder="Nhập lý do từ chối..."
                                               required>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-danger">Xác nhận từ chối</button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">Chưa có đơn đăng ký.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
