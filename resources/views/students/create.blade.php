@extends('layouts.app')

@section('title', 'Thêm hồ sơ sinh viên')

@section('content')
<div class="page-header">
    <div>
        <h1>Thêm hồ sơ sinh viên</h1>
        <p>Chọn một tài khoản sinh viên chưa có hồ sơ.</p>
    </div>
</div>

<form method="POST" action="{{ route('students.store') }}">
    @csrf
    <div class="card">
        @include('students._form')
        <div class="actions">
            <button class="btn btn-primary">Lưu hồ sơ</button>
            <a class="btn btn-light" href="{{ route('students.index') }}">Quay lại</a>
        </div>
    </div>
</form>
@endsection
