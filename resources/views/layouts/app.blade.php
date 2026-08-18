<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quản lý ký túc xá')</title>
    <style>
        * { box-sizing: border-box; }
        body {
            margin: 0;
            font-family: Arial, Helvetica, sans-serif;
            background: #f4f7fb;
            color: #1f2937;
        }
        a { color: inherit; text-decoration: none; }
        .app { min-height: 100vh; display: flex; }
        .sidebar {
            width: 250px;
            background: #123a63;
            color: white;
            padding: 24px 16px;
            position: fixed;
            left: 0;
            top: 0;
            bottom: 0;
        }
        .brand {
            font-size: 20px;
            font-weight: 700;
            line-height: 1.4;
            padding: 0 10px 22px;
            border-bottom: 1px solid rgba(255,255,255,.15);
        }
        .brand small {
            display: block;
            font-size: 12px;
            font-weight: 400;
            opacity: .8;
            margin-top: 5px;
        }
        .menu { margin-top: 20px; }
        .menu a {
            display: block;
            padding: 12px 14px;
            margin-bottom: 7px;
            border-radius: 8px;
            color: #e8f1fa;
        }
        .menu a:hover, .menu a.active {
            background: rgba(255,255,255,.14);
            color: #fff;
        }
        .main {
            margin-left: 250px;
            width: calc(100% - 250px);
            min-height: 100vh;
        }
        .topbar {
            height: 68px;
            background: #fff;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 28px;
        }
        .topbar strong { color: #123a63; }
        .content { padding: 28px; }
        .page-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 16px;
            margin-bottom: 22px;
        }
        .page-header h1 { margin: 0; font-size: 25px; color: #123a63; }
        .page-header p { margin: 6px 0 0; color: #6b7280; }
        .card {
            background: #fff;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 3px 12px rgba(15, 23, 42, .04);
            padding: 20px;
            margin-bottom: 20px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        .stat { padding: 20px; }
        .stat .number {
            font-size: 31px;
            font-weight: 700;
            color: #123a63;
            margin-bottom: 7px;
        }
        .stat .label { color: #6b7280; }
        .form-grid {
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 16px;
        }
        .form-group { margin-bottom: 15px; }
        label {
            display: block;
            font-weight: 600;
            margin-bottom: 7px;
            font-size: 14px;
        }
        input, select, textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #cbd5e1;
            border-radius: 7px;
            background: #fff;
            font-size: 14px;
        }
        textarea { min-height: 95px; resize: vertical; }
        input:focus, select:focus, textarea:focus {
            outline: none;
            border-color: #3478b9;
            box-shadow: 0 0 0 3px rgba(52,120,185,.12);
        }
        .actions { display: flex; gap: 8px; flex-wrap: wrap; align-items: center; }
        .btn {
            display: inline-block;
            border: none;
            border-radius: 7px;
            padding: 9px 14px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
        }
        .btn-primary { background: #1f6fb2; color: #fff; }
        .btn-success { background: #198754; color: #fff; }
        .btn-danger { background: #c0392b; color: #fff; }
        .btn-warning { background: #d99200; color: #fff; }
        .btn-secondary { background: #64748b; color: #fff; }
        .btn-light { background: #e8eef5; color: #24415e; }
        .table-wrap { overflow-x: auto; }
        table { width: 100%; border-collapse: collapse; }
        th, td {
            padding: 12px 10px;
            border-bottom: 1px solid #e5e7eb;
            text-align: left;
            vertical-align: middle;
            font-size: 14px;
        }
        th { background: #f8fafc; color: #475569; }
        .badge {
            display: inline-block;
            border-radius: 999px;
            padding: 5px 9px;
            font-size: 12px;
            font-weight: 700;
        }
        .badge-pending { background: #fff3cd; color: #8a6100; }
        .badge-approved { background: #d1e7dd; color: #0f5132; }
        .badge-rejected { background: #f8d7da; color: #842029; }
        .badge-active { background: #d1e7dd; color: #0f5132; }
        .badge-inactive { background: #e5e7eb; color: #4b5563; }
        .alert {
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 16px;
        }
        .alert-success { background: #d1e7dd; color: #0f5132; }
        .alert-error { background: #f8d7da; color: #842029; }
        .errors { margin: 8px 0 0; padding-left: 20px; }
        .filters {
            display: grid;
            grid-template-columns: 2fr repeat(3, 1fr) auto;
            gap: 10px;
            align-items: end;
        }
        .muted { color: #6b7280; }
        .detail-grid {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 10px 18px;
        }
        .detail-grid .key { font-weight: 700; color: #475569; }
        .pagination { margin-top: 18px; }
        .pagination nav { display: flex; }
        .pagination svg { width: 18px; }
        @media (max-width: 900px) {
            .sidebar { position: static; width: 100%; }
            .app { display: block; }
            .main { margin-left: 0; width: 100%; }
            .grid, .form-grid, .filters { grid-template-columns: 1fr; }
        }
    </style>
</head>
<body>
<div class="app">
    <aside class="sidebar">
        <div class="brand">
            KTX TLU
            <small>Quản lý ký túc xá & đăng ký chỗ ở</small>
        </div>

        <nav class="menu">
            <a href="{{ route('dashboard') }}"
               class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                Tổng quan
            </a>

            @if(auth()->user()->role === 'admin')
                <a href="{{ route('users.index') }}"
                   class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                    Tài khoản người dùng
                </a>
            @endif

            @if(in_array(auth()->user()->role, ['admin', 'staff']))
                <a href="{{ route('students.index') }}"
                   class="{{ request()->routeIs('students.*') ? 'active' : '' }}">
                    Hồ sơ sinh viên
                </a>
            @endif

            <a href="{{ route('room-registrations.index') }}"
               class="{{ request()->routeIs('room-registrations.*') ? 'active' : '' }}">
                {{ auth()->user()->role === 'student' ? 'Đăng ký của tôi' : 'Đăng ký chỗ ở' }}
            </a>
        </nav>
    </aside>

    <main class="main">
        <div class="topbar">
            <strong>NHÓM 8 — CSE485</strong>
            <div class="actions">
                <span class="muted">{{ auth()->user()->name }} ({{ auth()->user()->role }})</span>
                <form method="POST" action="{{ route('logout') }}">
                    @csrf
                    <button class="btn btn-light">Đăng xuất</button>
                </form>
            </div>
        </div>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">{{ session('success') }}</div>
            @endif

            @if(session('error'))
                <div class="alert alert-error">{{ session('error') }}</div>
            @endif

            @if($errors->any())
                <div class="alert alert-error">
                    <strong>Dữ liệu chưa hợp lệ:</strong>
                    <ul class="errors">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
            @endif

            @yield('content')
        </div>
    </main>
</div>
</body>
</html>
