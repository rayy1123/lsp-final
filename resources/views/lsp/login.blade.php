<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Portal LSP - Administrator</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/astro-dark.css') }}">
    <style>
        .login-wrap {
            min-height: 100vh;
            display: grid;
            place-items: center;
            padding: 1.5rem;
            background: radial-gradient(circle at 50% 0%, #eef2ff, #f8fafc 60%);
        }
        .login-card {
            width: 100%;
            max-width: 420px;
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 2.25rem 2rem;
            box-shadow: 0 10px 25px -5px rgba(15, 23, 42, 0.08), 0 8px 10px -6px rgba(15, 23, 42, 0.05);
        }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="login-card">
            <!-- Header Brand Login -->
            <div style="text-align: center; margin-bottom: 2rem;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo LSP" style="width: 64px; height: 64px; object-fit: cover; border-radius: var(--radius-md); box-shadow: 0 6px 16px rgba(0,0,0,0.2); background: #000; margin: 0 auto 1rem;">
                <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); letter-spacing: -0.02em;">LSP CERTIFICATION</h1>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.35rem;">Portal Administrator LSP &amp; TUK</p>
            </div>

            <!-- Pesan Error Gagal Login -->
            @if($errors->has('login'))
                <div style="background: var(--accent-rose-bg); border: 1px solid var(--accent-rose-border); color: var(--accent-rose); padding: 0.75rem 1rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 500; margin-bottom: 1.25rem;">
                    {{ $errors->first('login') }}
                </div>
            @endif

            <!-- Form Autentikasi -->
            <form action="{{ route('lsp.login.post') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label">Username atau Email</label>
                    <input type="text" name="email" class="form-control" placeholder="Masukkan username / email..." value="admin" required autofocus>
                </div>

                <div class="form-group" style="margin-bottom: 1.75rem;">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.45rem;">
                        <label class="form-label" style="margin-bottom: 0;">Password</label>
                    </div>
                    <input type="password" name="password" class="form-control" placeholder="••••••••" value="admin123" required>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 0.925rem;">
                    Masuk ke Dashboard
                </button>
            </form>

            <div style="margin-top: 1.25rem; text-align: center;">
                <a href="{{ route('lsp.landing') }}" style="font-size: 0.825rem; color: var(--text-muted); text-decoration: none;">
                    &larr; Kembali ke Beranda Landing Page
                </a>
            </div>

            <!-- Footer Info Kredensial Pengujian -->
            <div style="margin-top: 1.5rem; padding-top: 1.25rem; border-top: 1px solid var(--border-subtle); text-align: center; font-size: 0.75rem; color: var(--text-muted);">
                Kredensial Default: <code style="font-family: var(--font-mono); font-weight: 600; color: var(--text-primary); background: var(--bg-surface-elevated); padding: 0.15rem 0.4rem; border-radius: 4px;">admin</code> / <code style="font-family: var(--font-mono); font-weight: 600; color: var(--text-primary); background: var(--bg-surface-elevated); padding: 0.15rem 0.4rem; border-radius: 4px;">admin123</code>
            </div>
        </div>
    </div>
</body>
</html>
