@extends('layouts.app')

@section('title', 'Sửa đơn đăng ký')

@section('content')
<div class="page-header">
    <div>
        <h1>Sửa đơn đăng ký</h1>
        <p>Chỉ đơn đang chờ duyệt mới có thể chỉnh sửa.</p>
    </div>
</div>

<form method="POST" action="{{ route('room-registrations.update', $roomRegistration) }}">
    @csrf
    @method('PUT')
    <div class="card">
        @include('room-registrations._form')
        <div class="actions">
            <button class="btn btn-primary">Cập nhật đơn</button>
            <a class="btn btn-light"
               href="{{ route('room-registrations.show', $roomRegistration) }}">
                Quay lại
            </a>
        </div>
    </div>
</form>
@endsection
