@extends('layouts.app')

@section('title', 'Quản lý Tòa nhà')

@section('content')
<div class="row g-4">
    <div class="col-lg-4">
        <div class="card shadow-sm">
            <div class="card-header bg-white text-dark">
                <i class="bi bi-plus-circle text-primary me-2"></i> Thêm Tòa nhà mới
            </div>
            <div class="card-body">
                <form action="{{ route('buildings.store') }}" method="POST">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mã tòa nhà</label>
                        <input type="text" name="code" class="form-control" placeholder="VD: T1, A..." required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Tên tòa nhà</label>
                        <input type="text" name="name" class="form-control" placeholder="VD: Tòa A1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Trạng thái</label>
                        <select name="status" class="form-select">
                            <option value="active">Hoạt động</option>
                            <option value="maintenance">Bảo trì</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-semibold">Mô tả</label>
                        <textarea name="description" class="form-control" rows="3" placeholder="Nhập mô tả thêm..."></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary w-100 py-2 shadow-sm">
                        <i class="bi bi-save me-1"></i> Lưu Tòa nhà
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="card shadow-sm">
            <div class="card-header bg-white d-flex justify-content-between align-items-center">
                <span class="fw-bold"><i class="bi bi-building me-2 text-secondary"></i> Danh sách Tòa nhà</span>
                <span class="badge bg-light text-dark border px-3 py-2">Tổng số: {{ count($buildings) }}</span>
            </div>
            <div class="card-body p-0">
                <div class="table-responsive">
                    <table class="table table-hover mb-0 align-middle">
                        <thead class="table-light text-uppercase fs-7">
                            <tr>
                                <th class="ps-3">Mã</th>
                                <th>Tên tòa nhà</th>
                                <th class="text-center">Số phòng</th>
                                <th>Trạng thái</th>
                                <th class="text-end pe-3">Hành động</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($buildings as $building)
                            <tr>
                                <td class="ps-3 fw-bold text-primary">{{ $building->code }}</td>
                                <td>
                                    <div class="fw-semibold">{{ $building->name }}</div>
                                    <small class="text-muted">{{ Str::limit($building->description, 30) }}</small>
                                </td>
                                <td class="text-center">
                                    <span class="badge bg-info bg-opacity-10 text-info px-2 py-1">{{ $building->rooms_count }} phòng</span>
                                </td>
                                <td>
                                    @if($building->status == 'active')
                                        <span class="badge bg-success bg-opacity-10 text-success px-2 py-1">Hoạt động</span>
                                    @else
                                        <span class="badge bg-warning bg-opacity-10 text-warning px-2 py-1">Bảo trì</span>
                                    @endif
                                </td>
                                <td class="text-end pe-3">
                                    <form action="{{ route('buildings.destroy', $building->id) }}" method="POST" class="d-inline" onsubmit="return confirm('Bạn có chắc chắn muốn xóa tòa nhà này?');">
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
                                <td colspan="5" class="text-center py-4 text-muted">Chưa có dữ liệu tòa nhà nào.</td>
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