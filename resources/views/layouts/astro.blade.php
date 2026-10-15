<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'LSP Certification Portal')</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <!-- Google Fonts: Inter & JetBrains Mono -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/astro-dark.css') }}">
    <style>
        .brand-logo-img {
            width: 38px;
            height: 38px;
            object-fit: contain;
            background: transparent;
        }
    </style>
</head>
<body>
    <div class="app-container">
        <!-- Sidebar Navigasi Utama -->
        <aside class="sidebar">
            <div>
                <!-- Brand / Logo Aplikasi -->
                <div class="brand">
                    <img src="{{ asset('images/logo.png') }}" alt="Logo LSP" class="brand-logo-img">
                    <div class="brand-text">
                        <h1>Dashboard</h1>
                        <span>PORTAL SERTIFIKASI</span>
                    </div>
                </div>

                <!-- Menu Navigasi Modul -->
                <ul class="nav-menu">
                    <li>
                        <a href="{{ route('lsp.dashboard') }}" class="nav-link {{ request()->routeIs('lsp.dashboard') ? 'active' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M3.75 6A2.25 2.25 0 016 3.75h2.25A2.25 2.25 0 0110.5 6v2.25a2.25 2.25 0 01-2.25 2.25H6a2.25 2.25 0 01-2.25-2.25V6zM3.75 15.75A2.25 2.25 0 016 13.5h2.25a2.25 2.25 0 012.25 2.25V18a2.25 2.25 0 01-2.25 2.25H6A2.25 2.25 0 013.75 18v-2.25zM13.5 6a2.25 2.25 0 012.25-2.25H18A2.25 2.25 0 0120.25 6v2.25A2.25 2.25 0 0118 10.5h-2.25a2.25 2.25 0 01-2.25-2.25V6zM13.5 15.75a2.25 2.25 0 012.25-2.25H18a2.25 2.25 0 012.25 2.25V18A2.25 2.25 0 0118 20.25h-2.25A2.25 2.25 0 0113.5 18v-2.25z" />
                            </svg>
                            {{ Auth::user()->isAdmin() ? 'Dashboard Admin' : 'Selamat Datang' }}
                        </a>
                    </li>
                    <li>
                        <a href="{{ route('lsp.schemes') }}" class="nav-link {{ request()->routeIs('lsp.schemes') ? 'active' : '' }}">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                            </svg>
                            Skema Sertifikasi
                        </a>
                    </li>

                    @if(Auth::user()->isAdmin())
                        <li>
                            <a href="{{ route('lsp.participants') }}" class="nav-link {{ request()->routeIs('lsp.participants') ? 'active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
                                </svg>
                                Peserta Sertifikasi
                            </a>
                        </li>
                        <li>
                            <a href="{{ route('lsp.report') }}" class="nav-link {{ request()->routeIs('lsp.report') ? 'active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M6.72 13.829c-.24-1.076-.672-2.03-1.272-2.829H4.5v-3.75a3 3 0 013-3h9a3 3 0 013 3v3.75h-.948c-.6 0-1.032.953-1.272 2.829m-10.56 0A5.992 5.992 0 006 18v2.25h12V18c0-1.61-.634-3.074-1.68-4.171m-9.6 0h9.6M6.75 6.75h10.5" />
                                </svg>
                                Print Laporan USK
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{ route('lsp.assessment.result') }}" class="nav-link {{ request()->routeIs('lsp.assessment.result') ? 'active' : '' }}">
                                <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                                </svg>
                                Hasil Asesmen
                            </a>
                        </li>
                    @endif

                    <li style="margin-top: 1rem; border-top: 1px solid var(--border-subtle); padding-top: 0.5rem;">
                        <a href="{{ route('lsp.landing') }}" class="nav-link">
                            <svg fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" d="M2.25 12l8.954-8.955c.44-.439 1.152-.439 1.591 0L21.75 12M4.5 9.75v10.125c0 .621.504 1.125 1.125 1.125H9.75v-4.875c0-.621.504-1.125 1.125-1.125h2.25c.621 0 1.125.504 1.125 1.125V21h4.125c.621 0 1.125-.504 1.125-1.125V9.75M8.25 21h8.25" />
                            </svg>
                            Halaman Utama
                        </a>
                    </li>
                </ul>
            </div>

            <!-- Bagian Profil User & Tombol Logout Proporsional -->
            <div class="sidebar-footer" style="display: flex; flex-direction: column; gap: 0.85rem; padding-top: 1.25rem; border-top: 1px solid var(--border-subtle);">
                <div style="display: flex; align-items: flex-start; gap: 0.75rem; width: 100%;">
                    <div class="avatar" style="flex-shrink: 0;">
                        {{ strtoupper(substr(Auth::user()->name ?? 'A', 0, 1)) }}
                    </div>
                    <div style="flex: 1; min-width: 0;">
                        <div style="display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap; margin-bottom: 0.15rem;">
                            <span style="font-size: 0.85rem; font-weight: 700; color: var(--text-primary); word-break: break-word; line-height: 1.25;">
                                {{ Auth::user()->name ?? 'Pengguna' }}
                            </span>
                            <span class="badge" style="font-size: 0.65rem; padding: 0.1rem 0.4rem; {{ Auth::user()->isAdmin() ? 'background: var(--accent-primary-light); color: var(--accent-primary); border: 1px solid var(--accent-primary-border);' : 'background: var(--accent-emerald-bg); color: var(--accent-emerald); border: 1px solid var(--accent-emerald-border);' }}">
                                {{ Auth::user()->isAdmin() ? 'ADMIN' : 'ASESI' }}
                            </span>
                        </div>
                        <div style="font-size: 0.725rem; color: var(--text-muted); font-family: var(--font-mono); word-break: break-all;">
                            {{ Auth::user()->email ?? 'user@lsp.id' }}
                        </div>
                    </div>
                </div>

                <form action="{{ route('lsp.logout') }}" method="POST" style="width: 100%; margin: 0;" onsubmit="return confirm('Konfirmasi Keluar: Apakah Anda yakin ingin keluar dari sistem LSP?')">
                    @csrf
                    <button type="submit" class="btn btn-secondary" style="width: 100%; display: flex; align-items: center; justify-content: center; gap: 0.5rem; padding: 0.55rem 0.75rem; font-size: 0.8rem; border-color: #fecdd3; color: #e11d48; background: #fff1f2; font-weight: 600; border-radius: var(--radius-sm); transition: all 0.15s ease;" title="Keluar dari Sistem LSP">
                        <svg style="width: 15px; height: 15px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 9V5.25A2.25 2.25 0 0013.5 3h-6a2.25 2.25 0 00-2.25 2.25v13.5A2.25 2.25 0 007.5 21h6a2.25 2.25 0 002.25-2.25V15M12 9l-3 3m0 0l3 3m-3-3h12.75" />
                        </svg>
                        <span>Keluar Sistem</span>
                    </button>
                </form>
            </div>
        </aside>

        <!-- Area Workspace Konten Utama -->
        <main class="main-content">
            <!-- Top Status Indicator -->
            <div class="top-header">
                <div style="font-size: 0.85rem; font-weight: 600; color: var(--text-muted); font-family: var(--font-mono);">
                    {{ date('l, d F Y') }}
                </div>
                <div>
                    <a href="{{ route('lsp.landing') }}" class="btn btn-secondary" style="font-size: 0.75rem; padding: 0.35rem 0.75rem;">
                        Lihat Landing Page &rarr;
                    </a>
                </div>
            </div>

            <!-- Notifikasi Pesan Sukses -->
            @if(session('success'))
                <div style="border: 1px solid var(--accent-emerald-border); background: var(--accent-emerald-bg); padding: 0.85rem 1.25rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; color: var(--accent-emerald); font-weight: 600; font-size: 0.875rem; display: flex; align-items: center; gap: 0.5rem; box-shadow: var(--shadow-sm);">
                    <svg style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                    {{ session('success') }}
                </div>
            @endif

            <!-- Notifikasi Error Validasi -->
            @if($errors->any())
                <div style="border: 1px solid var(--accent-rose-border); background: var(--accent-rose-bg); padding: 0.85rem 1.25rem; border-radius: var(--radius-sm); margin-bottom: 1.5rem; color: var(--accent-rose); font-size: 0.875rem; box-shadow: var(--shadow-sm);">
                    <div style="font-weight: 700; margin-bottom: 0.35rem;">Terdapat kesalahan pada input:</div>
                    @foreach($errors->all() as $err)
                        <div style="font-size: 0.825rem;">• {{ $err }}</div>
                    @endforeach
                </div>
            @endif

            <!-- Konten Halaman Tertentu -->
            @yield('content')
        </main>
    </div>

    <script>
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

    @stack('scripts')
</body>
</html>
