@extends('layouts.app')

@section('title', 'Sửa hồ sơ sinh viên')

@section('content')
<div class="page-header">
    <div>
        <h1>Sửa hồ sơ sinh viên</h1>
        <p>{{ $student->student_code }} — {{ $student->user?->name }}</p>
    </div>
</div>

<form method="POST" action="{{ route('students.update', $student) }}">
    @csrf
    @method('PUT')
    <div class="card">
        @include('students._form')
        <div class="actions">
            <button class="btn btn-primary">Cập nhật</button>
            <a class="btn btn-light" href="{{ route('students.show', $student) }}">Quay lại</a>
        </div>
    </div>
</form>
@endsection
