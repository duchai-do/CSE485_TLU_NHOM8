@extends('layouts.app')

@section('title', 'Quản lý Phòng')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white">
                <i class="bi bi-plus-circle text-primary me-2"></i> Thêm Phòng mới
            </div>
            <div class="card-body">
                <form action="{{ route('rooms.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tòa nhà</label>
                        <select name="building_id" class="form-select" required>
                            <option value="">-- Chọn tòa nhà --</option>
                            @foreach($buildings as $b)
                                <option value="{{ $b->id }}">{{ $b->name }} ({{ $b->code }})</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Số phòng</label>
                        <input type="text" name="room_number" class="form-control" placeholder="VD: 101, 202..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Loại phòng</label>
                        <select name="type" class="form-select">
                            <option value="male">Nam</option>
                            <option value="female">Nữ</option>
                            <option value="other">Khác</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Sức chứa tối đa (Giường)</label>
                        <input type="number" name="capacity" class="form-control" min="1" placeholder="VD: 4" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Giá phòng (VNĐ/tháng)</label>
                        <input type="number" name="price" class="form-control" step="1000" placeholder="VD: 1500000" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="available">Còn trống</option>
                            <option value="full">Đã đầy</option>
                            <option value="maintenance">Bảo trì</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm">
                        <i class="bi bi-save me-1"></i> Lưu Phòng
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-door-open me-2 text-secondary"></i> Danh sách Phòng</span>
                <span class="badge bg-light text-dark border px-3 py-2">Tổng số: {{ count($rooms) }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light text-uppercase fs-7">
                            <tr>
                                <th class="ps-3">Tòa / Phòng</th>
                                <th>Loại</th>
                                <th class="text-center">Sức chứa</th>
                                <th>Giá phòng</th>
                                <th>Trạng thái</th>
                                <th class="text-end pe-3">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($rooms as $room)
                            <tr>
                                <td class="ps-3">
                                    <div class="fw-bold text-primary">Phòng {{ $room->room_number }}</div>
                                    <small class="text-muted"><i class="bi bi-building"></i> {{ $room->building->name ?? 'N/A' }}</small>
                                </td>
                                <td>
                                    <span class="badge bg-secondary bg-opacity-10 text-secondary">
                                        {{ ucfirst($room->type) }}
                                    </span>
                                </td>
                                <td class="text-center">
                                    <span class="fw-semibold">{{ $room->beds_count }} / {{ $room->capacity }}</span>
                                </td>
                                <td class="fw-semibold text-success">{{ number_format($room->price) }} đ</td>
                                <td>
                                    @if($room->status == 'available')
                                        <span class="badge bg-success bg-opacity-10 text-success">Còn trống</span>
                                    @elseif($room->status == 'full')
                                        <span class="badge bg-danger bg-opacity-10 text-danger">Đã đầy</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning">Bảo trì</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <form action="{{ route('rooms.destroy', $room->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa phòng này?');">
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
                                <td colspan="6" class="text-center py-4 text-muted">Chưa có dữ liệu phòng nào.</td>
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