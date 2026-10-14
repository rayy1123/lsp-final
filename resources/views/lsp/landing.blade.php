<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>LSP USK - Portal Sertifikasi Profesi</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/astro-dark.css') }}">
    <style>
        .page-wrap {
            max-width: 860px;
            margin: 0 auto;
            padding: 0 1.25rem 3rem;
        }

        .landing-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 1.25rem 0;
            border-bottom: 1px solid var(--border-subtle);
            margin-bottom: 2rem;
        }

        .brand-wrap {
            display: flex;
            align-items: center;
            gap: 0.65rem;
            text-decoration: none;
            color: inherit;
        }

        .brand-logo {
            width: 34px;
            height: 34px;
            object-fit: contain;
            background: transparent;
        }

        .brand-title {
            font-weight: 700;
            font-size: 0.95rem;
            color: var(--text-primary);
            line-height: 1.2;
        }

        .brand-sub {
            font-size: 0.675rem;
            font-family: var(--font-mono);
            color: var(--text-muted);
            letter-spacing: 0.03em;
        }

        .hero-compact {
            padding: 1.5rem 0 2rem;
        }

        .welcome-badge {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.8rem;
            color: #3730a3;
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            padding: 0.35rem 0.65rem;
            border-radius: var(--radius-sm);
            margin-bottom: 0.75rem;
            font-weight: 500;
        }

        .welcome-badge-guest {
            display: inline-flex;
            align-items: center;
            gap: 0.4rem;
            font-size: 0.8rem;
            color: var(--text-secondary);
            background: #f8fafc;
            border: 1px solid var(--border-subtle);
            padding: 0.35rem 0.65rem;
            border-radius: var(--radius-sm);
            margin-bottom: 0.75rem;
            font-weight: 500;
        }

        .hero-title {
            font-size: 1.75rem;
            font-weight: 800;
            letter-spacing: -0.025em;
            color: var(--text-primary);
            line-height: 1.25;
            margin-bottom: 0.5rem;
        }

        .hero-desc {
            font-size: 0.925rem;
            color: var(--text-secondary);
            line-height: 1.5;
            max-width: 600px;
            margin-bottom: 1.25rem;
        }

        .hero-cta {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            flex-wrap: wrap;
        }

        .schemes-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin: 2rem 0 0.75rem;
        }

        .schemes-title {
            font-size: 1rem;
            font-weight: 700;
            color: var(--text-primary);
        }

        .schemes-count {
            font-size: 0.75rem;
            font-family: var(--font-mono);
            color: var(--text-muted);
        }

        .landing-footer {
            margin-top: 3rem;
            padding-top: 1.25rem;
            border-top: 1px solid var(--border-subtle);
            font-size: 0.775rem;
            color: var(--text-muted);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="page-wrap">
        <!-- Header -->
        <header class="landing-header">
            <a href="{{ route('lsp.landing') }}" class="brand-wrap">
                <img src="{{ asset('images/logo.png') }}" alt="Logo LSP" class="brand-logo">
                <div>
                    <div class="brand-title">LSP USK</div>
                    <div class="brand-sub">LISENSI BNSP &bull; TUK SMKN 64</div>
                </div>
            </a>

            <div style="display: flex; align-items: center; gap: 0.5rem;">
                @auth
                    <span style="font-size: 0.8rem; color: var(--text-muted); display: inline-flex; align-items: center; gap: 0.35rem;">
                        <span style="font-weight: 600; color: var(--text-primary);">{{ Auth::user()->name }}</span>
                        <span class="badge badge-code" style="font-size: 0.65rem; padding: 0.1rem 0.35rem;">{{ strtoupper(Auth::user()->role ?? 'USER') }}</span>
                    </span>
                    <a href="{{ route('lsp.dashboard') }}" class="btn btn-primary" style="padding: 0.4rem 0.75rem; font-size: 0.8rem;">
                        Dashboard &rarr;
                    </a>
                    <a href="{{ route('logout') }}" class="btn btn-secondary" style="padding: 0.4rem 0.65rem; font-size: 0.8rem; color: var(--accent-rose);" title="Keluar">
                        Keluar
                    </a>
                @else
                    <a href="{{ route('lsp.login') }}" class="btn btn-secondary" style="padding: 0.4rem 0.75rem; font-size: 0.8rem;">
                        Masuk
                    </a>
                    <a href="{{ route('lsp.register') }}" class="btn btn-primary" style="padding: 0.4rem 0.75rem; font-size: 0.8rem;">
                        Daftar Asesi
                    </a>
                @endauth
            </div>
        </header>

        <!-- Flash Notice -->
        @if(session('success'))
            <div style="margin-bottom: 1.5rem; padding: 0.75rem 1rem; border-radius: var(--radius-sm); background: var(--accent-emerald-bg); border: 1px solid var(--accent-emerald-border); color: #065f46; font-size: 0.825rem; font-weight: 500;">
                {{ session('success') }}
            </div>
        @endif
        @if(session('error'))
            <div style="margin-bottom: 1.5rem; padding: 0.75rem 1rem; border-radius: var(--radius-sm); background: var(--accent-rose-bg); border: 1px solid var(--accent-rose-border); color: #9f1239; font-size: 0.825rem; font-weight: 500;">
                {{ session('error') }}
            </div>
        @endif

        <!-- Hero Sapaan Ringkas -->
        <section class="hero-compact">
            @auth
                <div class="welcome-badge">
                    <span style="width: 7px; height: 7px; border-radius: 50%; background: var(--accent-emerald); display: inline-block;"></span>
                    <span>Selamat datang kembali, <b>{{ Auth::user()->name }}</b></span>
                </div>
            @else
                <div class="welcome-badge-guest">
                    <span>Selamat datang di Portal Lembaga Sertifikasi Profesi (LSP) USK</span>
                </div>
            @endauth

            <h1 class="hero-title">
                Sertifikasi Profesi Standar BNSP
            </h1>
            <p class="hero-desc">
                Pelaksanaan uji kompetensi dan sertifikasi keahlian terstandar nasional di Tempat Uji Kompetensi (TUK) SMK Negeri 64 Jakarta.
            </p>

            <div class="hero-cta">
                @auth
                    <a href="{{ route('lsp.dashboard') }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                        Masuk ke Dashboard &rarr;
                    </a>
                    @if(!Auth::user()->isAdmin())
                        <a href="{{ route('lsp.schemes') }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                            Katalog Skema
                        </a>
                    @endif
                @else
                    <a href="{{ route('lsp.register') }}" class="btn btn-primary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                        Daftar Asesi Baru &rarr;
                    </a>
                    <a href="{{ route('lsp.login') }}" class="btn btn-secondary" style="padding: 0.5rem 1rem; font-size: 0.85rem;">
                        Masuk Portal
                    </a>
                @endauth
            </div>
        </section>

        <!-- Daftar Skema Sertifikasi Ringkas -->
        <section>
            <div class="schemes-header">
                <h2 class="schemes-title">Daftar Skema Sertifikasi</h2>
                <span class="schemes-count">{{ count($schemes) }} SKEMA TERSEDIA</span>
            </div>

            <div class="table-container">
                <table class="astro-table">
                    <thead>
                        <tr>
                            <th style="width: 120px;">Kode</th>
                            <th>Nama Skema</th>
                            <th style="width: 140px; text-align: right;">Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($schemes as $s)
                            <tr>
                                <td>
                                    <span class="badge badge-code" style="color: #4338ca; font-weight: 700;">{{ $s->scheme_code }}</span>
                                </td>
                                <td>
                                    <div style="font-weight: 600; color: var(--text-primary); font-size: 0.875rem;">{{ $s->scheme_name }}</div>
                                    @if($s->description)
                                        <div style="font-size: 0.775rem; color: var(--text-muted); margin-top: 0.15rem; line-height: 1.35;">
                                            {{ $s->description }}
                                        </div>
                                    @endif
                                </td>
                                <td style="text-align: right; white-space: nowrap;">
                                    @auth
                                        @if(!Auth::user()->isAdmin())
                                            @if($myParticipant && $myParticipant->scheme_id == $s->id)
                                                <span style="display: inline-flex; align-items: center; gap: 0.35rem; font-size: 0.775rem; font-weight: 600; color: #047857; background: #ecfdf5; border: 1px solid #a7f3d0; padding: 0.35rem 0.75rem; border-radius: var(--radius-sm); white-space: nowrap; line-height: 1.2;">
                                                    <span style="display: inline-block; width: 6px; height: 6px; border-radius: 50%; background: #059669;"></span>
                                                    Skema Aktif
                                                </span>
                                            @else
                                                <form action="{{ route('lsp.apply.scheme') }}" method="POST" style="display: inline-block;" onsubmit="return confirm('Pilih skema {{ $s->scheme_name }}?')">
                                                    @csrf
                                                    <input type="hidden" name="scheme_id" value="{{ $s->id }}">
                                                    <button type="submit" class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.775rem; white-space: nowrap;">
                                                        Pilih
                                                    </button>
                                                </form>
                                            @endif
                                        @else
                                            <a href="{{ route('lsp.schemes') }}" class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.775rem; white-space: nowrap;">
                                                Detail
                                            </a>
                                        @endif
                                    @else
                                        <a href="{{ route('lsp.register') }}" class="btn btn-secondary" style="padding: 0.35rem 0.75rem; font-size: 0.775rem; white-space: nowrap;">
                                            Daftar
                                        </a>
                                    @endauth
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="3" style="text-align: center; padding: 2rem 1rem; color: var(--text-muted); font-size: 0.825rem;">
                                    Belum ada skema sertifikasi aktif.
                                </td>
                            </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </section>

        <!-- Footer Ringkas Satu Baris -->
        <footer class="landing-footer">
            <div>&copy; {{ date('Y') }} LSP USK &bull; TUK SMKN 64 Jakarta</div>
            <div>
                <a href="{{ route('lsp.login') }}" style="color: var(--text-muted); text-decoration: none;">Login Sistem</a>
            </div>
        </footer>
    </div>

    <script>
        // Pastikan background hitam pada logo otomatis transparan
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
