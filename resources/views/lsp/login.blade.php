<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Masuk Portal - LSP System</title>
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
        .password-toggle-btn {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            cursor: pointer;
            color: var(--text-muted);
            padding: 0.25rem;
            display: flex;
            align-items: center;
            justify-content: center;
            transition: color 0.15s ease;
        }
        .password-toggle-btn:hover {
            color: var(--text-primary);
        }
    </style>
</head>
<body>
    <div class="login-wrap">
        <div class="login-card">
            <!-- Header Brand Login -->
            <div style="text-align: center; margin-bottom: 2rem;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo LSP" style="width: 64px; height: 64px; object-fit: contain; margin: 0 auto 1rem; background: transparent;">
                <h1 style="font-size: 1.35rem; font-weight: 800; color: var(--text-primary); letter-spacing: -0.02em;">LSP CERTIFICATION</h1>
                <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.35rem;">Portal Masuk Pengguna &amp; Asesor</p>
            </div>

            <!-- Pesan Sukses -->
            @if(session('success'))
                <div style="background: var(--accent-emerald-bg); border: 1px solid var(--accent-emerald-border); color: var(--accent-emerald); padding: 0.75rem 1rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 500; margin-bottom: 1.25rem;">
                    {{ session('success') }}
                </div>
            @endif

            <!-- Pesan Error Gagal Login -->
            @if($errors->has('login'))
                <div style="background: var(--accent-rose-bg); border: 1px solid var(--accent-rose-border); color: var(--accent-rose); padding: 0.75rem 1rem; border-radius: var(--radius-sm); font-size: 0.85rem; font-weight: 500; margin-bottom: 1.25rem;">
                    {{ $errors->first('login') }}
                </div>
            @endif

            <!-- Sesi Aktif Dari Tab Sebelumnya -->
            @if(isset($currentUser) && $currentUser)
                <div style="background: #f8fafc; border: 1px solid var(--border-subtle); padding: 0.85rem 1rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.35rem;">Sesi aktif saat ini:</div>
                    <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem;">
                        <span style="font-weight: 700; color: var(--text-primary); font-size: 0.9rem;">{{ $currentUser->name }}</span>
                        <span class="badge badge-code" style="font-size: 0.7rem;">{{ strtoupper($currentUser->role ?? 'USER') }}</span>
                    </div>
                    <div style="display: flex; gap: 0.5rem; margin-top: 0.75rem;">
                        <a href="{{ route('lsp.dashboard') }}" class="btn btn-primary" style="flex: 1; text-align: center; font-size: 0.8rem; padding: 0.4rem;">
                            Buka Dashboard &rarr;
                        </a>
                        <a href="{{ route('logout') }}" class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.4rem 0.75rem; color: var(--accent-rose);" title="Keluar dari sesi akun ini">
                            Ganti Akun
                        </a>
                    </div>
                </div>

                <div style="text-align: center; margin: 1rem 0; font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; display: flex; align-items: center; gap: 0.5rem;">
                    <span style="flex: 1; height: 1px; background: var(--border-subtle);"></span>
                    <span>Atau Masuk Akun Lain</span>
                    <span style="flex: 1; height: 1px; background: var(--border-subtle);"></span>
                </div>
            @endif

            <!-- Form Autentikasi -->
            <form action="{{ route('lsp.login.post') }}" method="POST">
                @csrf
                <div class="form-group">
                    <label class="form-label" for="login_email">Username atau Email</label>
                    <input type="text" name="email" id="login_email" class="form-control" placeholder="Masukkan username atau email..." value="{{ old('email') }}" required autofocus>
                </div>

                <div class="form-group" style="margin-bottom: 1.75rem;">
                    <label class="form-label" for="login_password">Password</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="login_password" class="form-control" placeholder="••••••••" style="padding-right: 2.75rem;" required>
                        <button type="button" class="password-toggle-btn" onclick="togglePassword('login_password', this)" aria-label="Lihat password" title="Lihat password">
                            <svg class="eye-open" style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg class="eye-closed" style="width: 18px; height: 18px; display: none;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 0.75rem; font-size: 0.925rem;">
                    Masuk ke Dashboard
                </button>
            </form>

            <div style="margin-top: 1.25rem; text-align: center; font-size: 0.825rem;">
                <span style="color: var(--text-muted);">Belum punya akun?</span>
                <a href="{{ route('lsp.register') }}" style="color: var(--accent-primary); font-weight: 600; text-decoration: none;">
                    Daftar Asesi Baru &rarr;
                </a>
            </div>

            <div style="margin-top: 0.75rem; text-align: center;">
                <a href="{{ route('lsp.landing') }}" style="font-size: 0.825rem; color: var(--text-muted); text-decoration: none;">
                    &larr; Kembali ke Beranda Landing Page
                </a>
            </div>
        </div>
    </div>

    <script>
        function togglePassword(inputId, btn) {
            var input = document.getElementById(inputId);
            if (!input) return;
            var isPass = input.type === 'password';
            input.type = isPass ? 'text' : 'password';

            var eyeOpen = btn.querySelector('.eye-open');
            var eyeClosed = btn.querySelector('.eye-closed');
            if (eyeOpen && eyeClosed) {
                eyeOpen.style.display = isPass ? 'none' : 'block';
                eyeClosed.style.display = isPass ? 'block' : 'none';
            }
            btn.setAttribute('aria-label', isPass ? 'Sembunyikan password' : 'Lihat password');
            btn.setAttribute('title', isPass ? 'Sembunyikan password' : 'Lihat password');
        }

        // Auto-transparansi logo
        document.addEventListener('DOMContentLoaded', function () {
            document.querySelectorAll('img[src*="logo.png"], img[src*="logo.jpeg"]').forEach(function(img) {
                img.style.background = 'transparent';
                var temp = new Image();
                temp.crossOrigin = 'anonymous';
                temp.src = img.src;
                temp.onload = function() {
                    try {
                        var c = document.createElement('canvas');
                        c.width = temp.naturalWidth || temp.width;
                        c.height = temp.naturalHeight || temp.height;
                        var ctx = c.getContext('2d');
                        ctx.drawImage(temp, 0, 0);
                        var imgData = ctx.getImageData(0, 0, c.width, c.height);
                        var d = imgData.data;
                        var modified = false;
                        for (var i = 0; i < d.length; i += 4) {
                            var maxV = Math.max(d[i], d[i+1], d[i+2]);
                            if (maxV < 35) {
                                d[i+3] = 0;
                                modified = true;
                            } else if (maxV < 65) {
                                d[i+3] = Math.round(255 * ((maxV - 35) / 30));
                                modified = true;
                            }
                        }
                        if (modified) {
                            ctx.putImageData(imgData, 0, 0);
                            img.src = c.toDataURL('image/png');
                        }
                    } catch(e) {}
                };
            });
        });
    </script>
</body>
</html>