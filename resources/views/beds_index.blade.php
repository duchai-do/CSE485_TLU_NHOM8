@extends('layouts.app')

@section('title', 'Quản lý Giường')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <i class="bi bi-plus-circle text-primary me-2"></i> Thêm Giường mới
            </div>
            <div class="card-body">
                <form action="{{ route('beds.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Chọn Phòng</label>
                        <select name="room_id" class="form-select" required>
                            <option value="">-- Chọn phòng --</option>
                            @foreach($rooms as $r)
                                <option value="{{ $r->id }}">Tòa {{ $r->building->name ?? '' }} - Phòng {{ $r->room_number }} (Tối đa: {{ $r->capacity }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Số / Mã giường</label>
                        <input type="text" name="bed_number" class="form-control" placeholder="VD: G1, G2..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="empty">Trống</option>
                            <option value="occupied">Đã có người ở</option>
                            <option value="maintenance">Bảo trì</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm">
                        <i class="bi bi-save me-1"></i> Lưu Giường
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-hdd-rack me-2 text-secondary"></i> Danh sách Giường</span>
                <span class="badge bg-light text-dark border px-3 py-2">Tổng số: {{ count($beds) }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light text-uppercase fs-7">
                            <tr>
                                <th class="ps-3">Mã giường</th>
                                <th>Phòng / Tòa nhà</th>
                                <th>Trạng thái</th>
                                <th class="text-end pe-3">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($beds as $bed)
                            <tr>
                                <td class="ps-3 fw-bold text-primary">
                                    <i class="bi bi-badge-ad me-1 text-secondary"></i> Giường {{ $bed->bed_number }}
                                </td>
                                <td>
                                    <div class="fw-semibold">Phòng {{ $bed->room->room_number ?? 'N/A' }}</div>
                                    <small class="text-muted">Tòa: {{ $bed->room->building->name ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    @if($bed->status == 'empty')
                                        <span class="badge bg-success bg-opacity-10 text-success">Trống</span>
                                    @elseif($bed->status == 'occupied')
                                        <span class="badge bg-primary bg-opacity-10 text-primary">Đã có người</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning">Bảo trì</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <form action="{{ route('beds.destroy', $bed->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa giường này?');">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-outline-danger btn-sm border-0" title="Xóa">
                                            <i class="bi bi-trash"></i>
                                        </button>
                                    </form>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center py-4 text-muted">Chưa có dữ liệu giường nào.</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection