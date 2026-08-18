<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Đăng nhập - KTX TLU</title>
    <style>
        * { box-sizing: border-box; }
        body { margin: 0; min-height: 100vh; display: grid; place-items: center; background: #eef4fa; font-family: Arial, Helvetica, sans-serif; color: #1f2937; }
        .box { width: min(430px, calc(100% - 32px)); background: white; padding: 30px; border-radius: 14px; box-shadow: 0 12px 35px rgba(18,58,99,.12); }
        h1 { color: #123a63; margin-top: 0; }
        .muted { color: #6b7280; }
        label { display: block; font-weight: 700; margin: 15px 0 7px; }
        input { width: 100%; padding: 11px 12px; border: 1px solid #cbd5e1; border-radius: 8px; }
        button { width: 100%; border: 0; border-radius: 8px; padding: 11px; margin-top: 18px; background: #1f6fb2; color: white; font-weight: 700; cursor: pointer; }
        .alert { padding: 11px 13px; border-radius: 8px; margin: 12px 0; }
        .error { background: #f8d7da; color: #842029; }
        .success { background: #d1e7dd; color: #0f5132; }
        .remember { display: flex; align-items: center; gap: 8px; margin-top: 12px; }
        .remember input { width: auto; }
        .remember label { margin: 0; font-weight: 400; }
        .demo { margin-top: 20px; padding: 12px; background: #f8fafc; border-radius: 8px; font-size: 13px; line-height: 1.6; }
    </style>
</head>
<body>
<div class="box">
    <h1>Đăng nhập KTX TLU</h1>
    <p class="muted">Hệ thống quản lý ký túc xá và đăng ký chỗ ở.</p>

    @if(session('success'))
        <div class="alert success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert error">{{ $errors->first() }}</div>
    @endif

    <form method="POST" action="{{ route('login.submit') }}">
        @csrf
        <label>Email</label>
        <input type="email" name="email" value="{{ old('email') }}" required autofocus>

        <label>Mật khẩu</label>
        <input type="password" name="password" required>

        <div class="remember">
            <input id="remember" type="checkbox" name="remember" value="1">
            <label for="remember">Ghi nhớ đăng nhập</label>
        </div>

        <button>Đăng nhập</button>
    </form>

    <div class="demo">
        <strong>Tài khoản demo:</strong><br>
        Admin: admin@tlu.edu.vn / 12345678<br>
        Cán bộ: staff@tlu.edu.vn / 12345678<br>
        Sinh viên: an@tlu.edu.vn / 12345678
    </div>
</div>
</body>
</html>
