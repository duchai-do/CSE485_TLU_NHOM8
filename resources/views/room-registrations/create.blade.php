@extends('layouts.app')

@section('title', 'Tạo đơn đăng ký')

@section('content')
<div class="page-header">
    <div>
        <h1>Tạo đơn đăng ký chỗ ở</h1>
        <p>Đơn mới sẽ có trạng thái chờ duyệt.</p>
    </div>
</div>

<form method="POST" action="{{ route('room-registrations.store') }}">
    @csrf
    <div class="card">
        @include('room-registrations._form')
        <div class="actions">
            <button class="btn btn-primary">Gửi đơn đăng ký</button>
            <a class="btn btn-light" href="{{ route('room-registrations.index') }}">Quay lại</a>
        </div>
    </div>
</form>
@endsection
