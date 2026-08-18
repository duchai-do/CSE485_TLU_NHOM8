@extends('layouts.app')

@section('title', 'Hợp đồng')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2>Danh sách hợp đồng</h2>
    <a href="{{ route('member3.contracts.create') }}" class="btn btn-primary">Lập hợp đồng</a>
</div>

<form class="row g-2 mb-3">
    <div class="col-auto">
        <select name="status" class="form-select">
            <option value="">Tất cả trạng thái</option>
            @foreach(['active', 'expired', 'terminated'] as $status)
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
                    <th>Mã HĐ</th>
                    <th>Sinh viên</th>
                    <th>Phòng / giường</th>
                    <th>Thời hạn</th>
                    <th>Giá tháng</th>
                    <th>Trạng thái</th>
                    <th class="text-end">Thao tác</th>
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
                        <td>
                            <span class="badge text-bg-{{ $contract->status === 'active' ? 'success' : 'secondary' }}">
                                {{ $contract->status }}
                            </span>
                        </td>
                        <td class="text-end">
                            @if($contract->status !== 'terminated')
                                <a href="{{ route('member3.contracts.edit', $contract) }}" class="btn btn-sm btn-outline-primary">Sửa</a>
                            @endif

                            @if($contract->status === 'active')
                                <button class="btn btn-sm btn-outline-success"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#extend-{{ $contract->id }}">
                                    Gia hạn
                                </button>
                                <button class="btn btn-sm btn-outline-danger"
                                        data-bs-toggle="collapse"
                                        data-bs-target="#terminate-{{ $contract->id }}">
                                    Chấm dứt
                                </button>
                            @endif
                        </td>
                    </tr>

                    @if($contract->status === 'active')
                        <tr class="collapse" id="extend-{{ $contract->id }}">
                            <td colspan="7" class="bg-light">
                                <form method="POST" action="{{ route('member3.contracts.extend', $contract) }}" class="row g-2 align-items-end">
                                    @csrf
                                    @method('PATCH')
                                    <div class="col-md-4">
                                        <label class="form-label">Ngày kết thúc mới</label>
                                        <input type="date" name="new_end_date" class="form-control" required>
                                    </div>
                                    <div class="col-auto"><button class="btn btn-success">Xác nhận gia hạn</button></div>
                                </form>
                            </td>
                        </tr>
                        <tr class="collapse" id="terminate-{{ $contract->id }}">
                            <td colspan="7" class="bg-light">
                                <form method="POST" action="{{ route('member3.contracts.terminate', $contract) }}" class="row g-2">
                                    @csrf
                                    @method('PATCH')
                                    <div class="col">
                                        <input name="termination_reason" class="form-control" placeholder="Lý do chấm dứt..." required>
                                    </div>
                                    <div class="col-auto">
                                        <button class="btn btn-danger" onclick="return confirm('Chấm dứt hợp đồng và trả giường?')">
                                            Xác nhận chấm dứt
                                        </button>
                                    </div>
                                </form>
                            </td>
                        </tr>
                    @endif
                @empty
                    <tr><td colspan="7" class="text-center py-4 text-muted">Chưa có hợp đồng.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
