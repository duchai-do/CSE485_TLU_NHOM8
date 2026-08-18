<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'Quản lý ký túc xá')</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

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
            width: 270px;
            background: #123a63;
            color: white;
            padding: 24px 16px;
            position: fixed;
            inset: 0 auto 0 0;
            overflow-y: auto;
            z-index: 1000;
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
            padding: 11px 14px;
            margin-bottom: 6px;
            border-radius: 8px;
            color: #e8f1fa;
        }
        .menu a:hover, .menu a.active {
            background: rgba(255,255,255,.14);
            color: white;
        }
        .menu-title {
            padding: 14px 14px 6px;
            font-size: 11px;
            text-transform: uppercase;
            opacity: .65;
            letter-spacing: .5px;
        }
        .main {
            margin-left: 270px;
            width: calc(100% - 270px);
            min-height: 100vh;
        }
        .topbar {
            min-height: 68px;
            background: white;
            border-bottom: 1px solid #e5e7eb;
            display: flex;
            align-items: center;
            justify-content: space-between;
            gap: 15px;
            padding: 10px 28px;
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
        .page-header h1 {
            margin: 0;
            font-size: 25px;
            color: #123a63;
        }
        .page-header p {
            margin: 6px 0 0;
            color: #6b7280;
        }
        .card {
            background: white;
            border-radius: 12px;
            border: 1px solid #e5e7eb;
            box-shadow: 0 3px 12px rgba(15,23,42,.04);
            padding: 20px;
            margin-bottom: 20px;
        }
        .grid {
            display: grid;
            grid-template-columns: repeat(3, 1fr);
            gap: 16px;
        }
        .stat .number {
            font-size: 31px;
            font-weight: 700;
            color: #123a63;
        }
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
            background: white;
            font-size: 14px;
        }
        textarea { min-height: 95px; resize: vertical; }
        .actions {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
            align-items: center;
        }
        .table-wrap { overflow-x: auto; }
        .filters {
            display: grid;
            grid-template-columns: 2fr repeat(3,1fr) auto;
            gap: 10px;
            align-items: end;
        }
        .muted { color: #6b7280; }
        .detail-grid {
            display: grid;
            grid-template-columns: 180px 1fr;
            gap: 10px 18px;
        }
        .detail-grid .key {
            font-weight: 700;
            color: #475569;
        }
        .badge-pending { background: #fff3cd; color: #8a6100; }
        .badge-approved { background: #d1e7dd; color: #0f5132; }
        .badge-rejected { background: #f8d7da; color: #842029; }
        .badge-active { background: #d1e7dd; color: #0f5132; }
        .badge-inactive { background: #e5e7eb; color: #4b5563; }
        .alert-error {
            background: #f8d7da;
            color: #842029;
            padding: 12px 15px;
            border-radius: 8px;
            margin-bottom: 16px;
        }
        .errors { margin: 8px 0 0; padding-left: 20px; }
        .pagination { margin-top: 18px; }

        @media (max-width: 900px) {
            .sidebar {
                position: static;
                width: 100%;
            }
            .app { display: block; }
            .main {
                margin-left: 0;
                width: 100%;
            }
            .grid, .form-grid, .filters {
                grid-template-columns: 1fr !important;
            }
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
            @if(\Illuminate\Support\Facades\Route::has('dashboard'))
                <a href="{{ route('dashboard') }}"
                   class="{{ request()->routeIs('dashboard') ? 'active' : '' }}">
                    Tổng quan
                </a>
            @endif

            @if(auth()->user()->role === 'admin')
                @if(\Illuminate\Support\Facades\Route::has('users.index'))
                    <a href="{{ route('users.index') }}"
                       class="{{ request()->routeIs('users.*') ? 'active' : '' }}">
                        Tài khoản người dùng
                    </a>
                @endif
            @endif

            @if(in_array(auth()->user()->role, ['admin', 'staff']))
                <div class="menu-title">Sinh viên</div>

                @if(\Illuminate\Support\Facades\Route::has('students.index'))
                    <a href="{{ route('students.index') }}"
                       class="{{ request()->routeIs('students.*') ? 'active' : '' }}">
                        Hồ sơ sinh viên
                    </a>
                @endif

                @if(\Illuminate\Support\Facades\Route::has('room-registrations.index'))
                    <a href="{{ route('room-registrations.index') }}"
                       class="{{ request()->routeIs('room-registrations.*') ? 'active' : '' }}">
                        Đăng ký chỗ ở
                    </a>
                @endif

                <div class="menu-title">Phòng & giường</div>

                @if(\Illuminate\Support\Facades\Route::has('buildings.index'))
                    <a href="{{ route('buildings.index') }}"
                       class="{{ request()->routeIs('buildings.*') ? 'active' : '' }}">
                        Tòa nhà
                    </a>
                @endif

                @if(\Illuminate\Support\Facades\Route::has('rooms.index'))
                    <a href="{{ route('rooms.index') }}"
                       class="{{ request()->routeIs('rooms.*') ? 'active' : '' }}">
                        Phòng
                    </a>
                @endif

                @if(\Illuminate\Support\Facades\Route::has('beds.index'))
                    <a href="{{ route('beds.index') }}"
                       class="{{ request()->routeIs('beds.*') ? 'active' : '' }}">
                        Giường
                    </a>
                @endif

                <div class="menu-title">Xếp ở & hợp đồng</div>

                @if(\Illuminate\Support\Facades\Route::has('member3.registrations.index'))
                    <a href="{{ route('member3.registrations.index') }}"
                       class="{{ request()->routeIs('member3.registrations.*') ? 'active' : '' }}">
                        Xét duyệt đăng ký
                    </a>
                @endif

                @if(\Illuminate\Support\Facades\Route::has('member3.allocations.index'))
                    <a href="{{ route('member3.allocations.index') }}"
                       class="{{ request()->routeIs('member3.allocations.*') ? 'active' : '' }}">
                        Xếp giường
                    </a>
                @endif

                @if(\Illuminate\Support\Facades\Route::has('member3.contracts.index'))
                    <a href="{{ route('member3.contracts.index') }}"
                       class="{{ request()->routeIs('member3.contracts.*') ? 'active' : '' }}">
                        Hợp đồng
                    </a>
                @endif

                @if(\Illuminate\Support\Facades\Route::has('member3.violations.index'))
                    <a href="{{ route('member3.violations.index') }}"
                       class="{{ request()->routeIs('member3.violations.*') ? 'active' : '' }}">
                        Vi phạm
                    </a>
                @endif

                <div class="menu-title">Điện nước & hóa đơn</div>

                @if(\Illuminate\Support\Facades\Route::has('utility-readings.index'))
                    <a href="{{ route('utility-readings.index') }}"
                       class="{{ request()->routeIs('utility-readings.*') ? 'active' : '' }}">
                        Chỉ số điện nước
                    </a>
                @endif

                @if(\Illuminate\Support\Facades\Route::has('invoices.index'))
                    <a href="{{ route('invoices.index') }}"
                       class="{{ request()->routeIs('invoices.*') && !request()->routeIs('invoices.revenue') ? 'active' : '' }}">
                        Hóa đơn
                    </a>
                @endif

                @if(\Illuminate\Support\Facades\Route::has('invoices.revenue'))
                    <a href="{{ route('invoices.revenue') }}"
                       class="{{ request()->routeIs('invoices.revenue') ? 'active' : '' }}">
                        Doanh thu
                    </a>
                @endif
            @elseif(auth()->user()->role === 'student')
                @if(\Illuminate\Support\Facades\Route::has('students.my-profile') && auth()->user()->student)
                    <a href="{{ route('students.my-profile') }}"
                       class="{{ request()->routeIs('students.my-profile') ? 'active' : '' }}">
                        Hồ sơ của tôi
                    </a>
                @endif

                @if(\Illuminate\Support\Facades\Route::has('room-registrations.index'))
                    <a href="{{ route('room-registrations.index') }}"
                       class="{{ request()->routeIs('room-registrations.*') ? 'active' : '' }}">
                        Đăng ký của tôi
                    </a>
                @endif
            @endif
        </nav>
    </aside>

    <main class="main">
        <div class="topbar">
            <strong>NHÓM 8 — CSE485</strong>

            <div class="actions">
                <span class="muted">
                    {{ auth()->user()->name }}
                    ({{ auth()->user()->role }})
                </span>

                @if(\Illuminate\Support\Facades\Route::has('logout'))
                    <form method="POST" action="{{ route('logout') }}">
                        @csrf
                        <button class="btn btn-light">
                            Đăng xuất
                        </button>
                    </form>
                @endif
            </div>
        </div>

        <div class="content">
            @if(session('success'))
                <div class="alert alert-success">
                    {{ session('success') }}
                </div>
            @endif

            @if(session('error'))
                <div class="alert-error">
                    {{ session('error') }}
                </div>
            @endif

            @if($errors->any())
                <div class="alert-error">
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

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>