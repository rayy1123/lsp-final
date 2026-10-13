<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lembaga Sertifikasi Profesi (LSP) - Uji Sertifikasi Kompetensi</title>
    <link rel="icon" type="image/png" href="{{ asset('images/logo.png') }}">
    <!-- Fonts -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700;800&family=JetBrains+Mono:wght@400;500;600&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="{{ asset('css/astro-dark.css') }}">
    <style>
        .nav-logo-img {
            width: 40px;
            height: 40px;
            object-fit: cover;
            border-radius: var(--radius-sm);
            background: #000;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.15);
        }

        /* Landing Page Specific Styling */
        .landing-nav {
            position: sticky;
            top: 0;
            z-index: 100;
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(12px);
            border-bottom: 1px solid var(--border-subtle);
            padding: 1rem 2rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .nav-items {
            display: flex;
            align-items: center;
            gap: 1.5rem;
            list-style: none;
        }

        .nav-items a {
            text-decoration: none;
            color: var(--text-secondary);
            font-size: 0.9rem;
            font-weight: 500;
            transition: color 0.15s ease;
        }

        .nav-items a:hover {
            color: var(--accent-primary);
        }

        .hero-section {
            padding: 5rem 2rem 4rem;
            max-width: 1200px;
            margin: 0 auto;
            text-align: center;
            position: relative;
        }

        .hero-pill {
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            padding: 0.35rem 0.95rem;
            background: #eef2ff;
            border: 1px solid #c7d2fe;
            border-radius: var(--radius-full);
            color: #4338ca;
            font-size: 0.825rem;
            font-weight: 600;
            margin-bottom: 1.5rem;
        }

        .hero-title {
            font-size: 3rem;
            font-weight: 800;
            letter-spacing: -0.04em;
            line-height: 1.15;
            color: var(--text-primary);
            max-width: 860px;
            margin: 0 auto 1.5rem;
        }

        .hero-title span {
            background: linear-gradient(135deg, var(--accent-primary), #8b5cf6, var(--accent-astro));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }

        .hero-desc {
            font-size: 1.1rem;
            color: var(--text-muted);
            max-width: 680px;
            margin: 0 auto 2.5rem;
            line-height: 1.6;
        }

        .hero-cta {
            display: flex;
            justify-content: center;
            align-items: center;
            gap: 1rem;
            flex-wrap: wrap;
        }

        .landing-stat-strip {
            max-width: 1100px;
            margin: 3rem auto;
            padding: 0 1.5rem;
        }

        .stat-strip-box {
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-lg);
            padding: 2rem;
            box-shadow: var(--shadow-card);
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1.5rem;
            text-align: center;
        }

        .stat-strip-item {
            padding: 0.5rem 1rem;
            border-right: 1px solid var(--border-subtle);
        }

        .stat-strip-item:last-child {
            border-right: none;
        }

        .stat-strip-value {
            font-size: 2.25rem;
            font-weight: 800;
            font-family: var(--font-mono);
            color: var(--text-primary);
            line-height: 1.1;
        }

        .stat-strip-label {
            font-size: 0.825rem;
            color: var(--text-muted);
            font-weight: 600;
            margin-top: 0.35rem;
            text-transform: uppercase;
            letter-spacing: 0.04em;
        }

        .section-container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 4rem 2rem;
        }

        .section-header {
            text-align: center;
            margin-bottom: 3rem;
        }

        .section-tag {
            font-size: 0.8rem;
            font-weight: 700;
            text-transform: uppercase;
            letter-spacing: 0.08em;
            color: var(--accent-primary);
            margin-bottom: 0.5rem;
        }

        .section-heading {
            font-size: 2rem;
            font-weight: 800;
            color: var(--text-primary);
            letter-spacing: -0.03em;
        }

        .features-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(260px, 1fr));
            gap: 1.5rem;
        }

        .feature-card {
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 2rem;
            box-shadow: var(--shadow-card);
            transition: all 0.2s ease;
        }

        .feature-card:hover {
            transform: translateY(-3px);
            box-shadow: var(--shadow-lg);
            border-color: var(--border-strong);
        }

        .feature-icon {
            width: 48px;
            height: 48px;
            border-radius: var(--radius-sm);
            background: #eef2ff;
            color: #4f46e5;
            display: grid;
            place-items: center;
            margin-bottom: 1.25rem;
        }

        .scheme-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 1.5rem;
        }

        .scheme-card {
            background: #ffffff;
            border: 1px solid var(--border-subtle);
            border-radius: var(--radius-md);
            padding: 1.75rem;
            box-shadow: var(--shadow-card);
            display: flex;
            flex-direction: column;
            justify-content: space-between;
            transition: all 0.2s ease;
        }

        .scheme-card:hover {
            border-color: var(--border-focus);
            box-shadow: var(--shadow-lg);
            transform: translateY(-2px);
        }

        .cta-banner {
            background: linear-gradient(135deg, #09090b, #1e1b4b);
            color: #ffffff;
            border-radius: var(--radius-lg);
            padding: 3.5rem 2.5rem;
            text-align: center;
            margin: 4rem auto;
            max-width: 1100px;
            box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.3);
        }

        .landing-footer {
            border-top: 1px solid var(--border-subtle);
            background: #ffffff;
            padding: 3rem 2rem 2rem;
            font-size: 0.85rem;
            color: var(--text-muted);
        }

        @media (max-width: 768px) {
            .hero-title { font-size: 2.15rem; }
            .stat-strip-item { border-right: none; border-bottom: 1px solid var(--border-subtle); }
            .stat-strip-item:last-child { border-bottom: none; }
            .nav-items { display: none; }
        }
    </style>
</head>
<body>
    <!-- Top Navigation Bar -->
    <header class="landing-nav">
        <div style="display: flex; align-items: center; gap: 0.85rem;">
            <img src="{{ asset('images/logo.png') }}" alt="Logo LSP" class="nav-logo-img">
            <div>
                <span style="font-weight: 800; font-size: 1.05rem; letter-spacing: -0.02em; color: var(--text-primary);">LSP CERT USK</span>
                <span style="display: block; font-size: 0.7rem; font-family: var(--font-mono); color: var(--text-muted);">LEMBAGA SERTIFIKASI PROFESI</span>
            </div>
        </div>

        <ul class="nav-items">
            <li><a href="#hero">Beranda</a></li>
            <li><a href="#schemes">Skema Sertifikasi</a></li>
            <li><a href="#features">Keunggulan</a></li>
            <li><a href="#tuk">Tempat Uji (TUK)</a></li>
        </ul>

        <div style="display: flex; align-items: center; gap: 0.75rem;">
            @auth
                <a href="{{ route('lsp.dashboard') }}" class="btn btn-primary">
                    Buka Dashboard &rarr;
                </a>
            @else
                <a href="{{ route('lsp.login') }}" class="btn btn-secondary">
                    Masuk Portal
                </a>
                <a href="{{ route('lsp.login') }}" class="btn btn-primary">
                    Admin Login &rarr;
                </a>
            @endauth
        </div>
    </header>

    <!-- Hero Section -->
    <section class="hero-section" id="hero">
        <div style="margin-bottom: 1.5rem;">
            <img src="{{ asset('images/logo.png') }}" alt="Logo LSP" style="width: 90px; height: 90px; object-fit: cover; border-radius: 16px; box-shadow: 0 10px 25px rgba(0,0,0,0.25); background: #000; margin: 0 auto;">
        </div>
        <div class="hero-pill">
            <span style="width: 8px; height: 8px; border-radius: 50%; background: #4f46e5;"></span>
            Portal Resmi Uji Sertifikasi Kompetensi (USK)
        </div>
        <h1 class="hero-title">
            Standar Kompetensi Nasional untuk <span>Tenaga Kerja Unggul</span>
        </h1>
        <p class="hero-desc">
            Lembaga Sertifikasi Profesi (LSP) memfasilitasi asesmen dan sertifikasi kompetensi berbasis standar SKKNI dengan instrumen uji terakreditasi, transparan, dan terverifikasi secara resmi.
        </p>
        <div class="hero-cta">
            @auth
                <a href="{{ route('lsp.dashboard') }}" class="btn btn-primary" style="padding: 0.75rem 1.75rem; font-size: 0.95rem;">
                    Masuk ke Dashboard Asesmen
                </a>
            @else
                <a href="{{ route('lsp.login') }}" class="btn btn-primary" style="padding: 0.75rem 1.75rem; font-size: 0.95rem;">
                    Masuk ke Portal Asesmen
                </a>
            @endauth
            <a href="#schemes" class="btn btn-secondary" style="padding: 0.75rem 1.5rem; font-size: 0.95rem;">
                Lihat Skema Sertifikasi &darr;
            </a>
        </div>
    </section>

    <!-- Real-time Stats Strip -->
    <div class="landing-stat-strip">
        <div class="stat-strip-box">
            <div class="stat-strip-item">
                <div class="stat-strip-value" style="color: #4f46e5;">{{ $totalSchemes > 0 ? $totalSchemes : '3+' }}</div>
                <div class="stat-strip-label">Skema Sertifikasi</div>
            </div>
            <div class="stat-strip-item">
                <div class="stat-strip-value" style="color: var(--accent-emerald);">{{ $totalParticipants > 0 ? $totalParticipants : '100+' }}</div>
                <div class="stat-strip-label">Asesi Terdaftar</div>
            </div>
            <div class="stat-strip-item">
                <div class="stat-strip-value" style="color: #d97706;">{{ $totalKompeten > 0 ? $totalKompeten : '95%' }}</div>
                <div class="stat-strip-label">Rekomendasi Kompeten</div>
            </div>
            <div class="stat-strip-item">
                <div class="stat-strip-value" style="color: #7c3aed;">100%</div>
                <div class="stat-strip-label">Terverifikasi BNSP</div>
            </div>
        </div>
    </div>

    <!-- Modul Skema Sertifikasi Populer -->
    <section class="section-container" id="schemes">
        <div class="section-header">
            <div class="section-tag">Program Sertifikasi</div>
            <h2 class="section-heading">Skema Uji Kompetensi Pilihan</h2>
            <p style="color: var(--text-muted); font-size: 0.95rem; margin-top: 0.5rem;">
                Pilihan skema kompetensi terstandar industri untuk memvalidasi keahlian asesi.
            </p>
        </div>

        <div class="scheme-grid">
            @forelse($schemes as $s)
                <div class="scheme-card">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
                            <span class="badge badge-code" style="color: #4338ca; font-weight: 700;">{{ $s->scheme_code }}</span>
                            <span style="font-size: 0.775rem; color: var(--text-muted); font-family: var(--font-mono);">
                                {{ $s->participants_count ?? 0 }} Peserta
                            </span>
                        </div>
                        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                            {{ $s->scheme_name }}
                        </h3>
                        <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.25rem;">
                            {{ $s->description ?: 'Standar okupasi keahlian nasional terstruktur dan teruji.' }}
                        </p>
                    </div>
                    <div style="border-top: 1px solid var(--border-subtle); padding-top: 1rem; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.75rem; color: var(--accent-emerald); font-weight: 600;">✓ Terakreditasi BNSP</span>
                        <a href="{{ route('lsp.login') }}" style="font-size: 0.825rem; font-weight: 600; color: var(--accent-primary); text-decoration: none;">
                            Daftar Asesi &rarr;
                        </a>
                    </div>
                </div>
            @empty
                <!-- Fallback Cards jika database belum terisi -->
                <div class="scheme-card">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
                            <span class="badge badge-code" style="color: #4338ca; font-weight: 700;">JWD-2026</span>
                            <span style="font-size: 0.775rem; color: var(--text-muted); font-family: var(--font-mono);">Aktif</span>
                        </div>
                        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                            Junior Web Developer
                        </h3>
                        <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.25rem;">
                            Standar okupasi nasional bidang pemrograman terstruktur dan antarmuka web responsif.
                        </p>
                    </div>
                    <div style="border-top: 1px solid var(--border-subtle); padding-top: 1rem; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.75rem; color: var(--accent-emerald); font-weight: 600;">✓ Terakreditasi BNSP</span>
                        <a href="{{ route('lsp.login') }}" style="font-size: 0.825rem; font-weight: 600; color: var(--accent-primary); text-decoration: none;">
                            Daftar Asesi &rarr;
                        </a>
                    </div>
                </div>

                <div class="scheme-card">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
                            <span class="badge badge-code" style="color: #4338ca; font-weight: 700;">DMA-2026</span>
                            <span style="font-size: 0.775rem; color: var(--text-muted); font-family: var(--font-mono);">Aktif</span>
                        </div>
                        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                            Digital Marketing Analyst
                        </h3>
                        <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.25rem;">
                            Skema keahlian analitik data promosi dan strategi digital marketing berbasis konversi.
                        </p>
                    </div>
                    <div style="border-top: 1px solid var(--border-subtle); padding-top: 1rem; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.75rem; color: var(--accent-emerald); font-weight: 600;">✓ Terakreditasi BNSP</span>
                        <a href="{{ route('lsp.login') }}" style="font-size: 0.825rem; font-weight: 600; color: var(--accent-primary); text-decoration: none;">
                            Daftar Asesi &rarr;
                        </a>
                    </div>
                </div>

                <div class="scheme-card">
                    <div>
                        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem;">
                            <span class="badge badge-code" style="color: #4338ca; font-weight: 700;">NSA-2026</span>
                            <span style="font-size: 0.775rem; color: var(--text-muted); font-family: var(--font-mono);">Aktif</span>
                        </div>
                        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                            Network System Administrator
                        </h3>
                        <p style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5; margin-bottom: 1.25rem;">
                            Standar kompetensi pengelolaan infrastruktur jaringan, server, dan keamanan sistem informasi.
                        </p>
                    </div>
                    <div style="border-top: 1px solid var(--border-subtle); padding-top: 1rem; display: flex; justify-content: space-between; align-items: center;">
                        <span style="font-size: 0.75rem; color: var(--accent-emerald); font-weight: 600;">✓ Terakreditasi BNSP</span>
                        <a href="{{ route('lsp.login') }}" style="font-size: 0.825rem; font-weight: 600; color: var(--accent-primary); text-decoration: none;">
                            Daftar Asesi &rarr;
                        </a>
                    </div>
                </div>
            @endforelse
        </div>
    </section>

    <!-- Keunggulan Sistem -->
    <section class="section-container" style="background: #ffffff; border-top: 1px solid var(--border-subtle); border-bottom: 1px solid var(--border-subtle);" id="features">
        <div class="section-header">
            <div class="section-tag">Kualitas & Integritas</div>
            <h2 class="section-heading">Mengapa Mengikuti Sertifikasi LSP?</h2>
        </div>

        <div class="features-grid">
            <div class="feature-card">
                <div class="feature-icon">
                    <svg style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
                    </svg>
                </div>
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-primary);">Standar Resmi BNSP</h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">
                    Pengakuan legalitas kompetensi kerja berskala nasional dengan lisensi Badan Nasional Sertifikasi Profesi.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background: var(--accent-emerald-bg); color: var(--accent-emerald);">
                    <svg style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15.75 6a3.75 3.75 0 11-7.5 0 3.75 3.75 0 017.5 0zM4.501 20.118a7.5 7.5 0 0114.998 0A17.933 17.933 0 0112 21.75c-2.676 0-5.216-.584-7.499-1.632z" />
                    </svg>
                </div>
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-primary);">Asesor Tersertifikasi</h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">
                    Diuji langsung oleh Master Asesor dan praktisi industri yang memiliki registrasi resmi BNSP.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background: var(--accent-amber-bg); color: var(--accent-amber);">
                    <svg style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
                    </svg>
                </div>
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-primary);">Uji Praktik & Portofolio</h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">
                    Metode asesmen terpadu mencakup bukti langsung demonstrasi tugas kerja dan verifikasi portofolio asesi.
                </p>
            </div>

            <div class="feature-card">
                <div class="feature-icon" style="background: var(--accent-sky-bg); color: var(--accent-sky);">
                    <svg style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.72 13.829c-.24-1.076-.672-2.03-1.272-2.829H4.5v-3.75a3 3 0 013-3h9a3 3 0 013 3v3.75h-.948c-.6 0-1.032.953-1.272 2.829m-10.56 0A5.992 5.992 0 006 18v2.25h12V18c0-1.61-.634-3.074-1.68-4.171m-9.6 0h9.6M6.75 6.75h10.5" />
                    </svg>
                </div>
                <h3 style="font-size: 1.1rem; font-weight: 700; margin-bottom: 0.5rem; color: var(--text-primary);">Laporan & Berita Acara</h3>
                <p style="font-size: 0.85rem; color: var(--text-muted); line-height: 1.5;">
                    Penerbitan Berita Acara USK dan laporan rekapitulasi capaian kelulusan siap cetak otomatis.
                </p>
            </div>
        </div>
    </section>

    <!-- Info Tempat Uji Kompetensi (TUK) -->
    <section class="section-container" id="tuk">
        <div style="background: #ffffff; border: 1px solid var(--border-subtle); border-radius: var(--radius-lg); padding: 2.5rem; box-shadow: var(--shadow-card); display: grid; grid-template-columns: 1fr 1fr; gap: 2rem; align-items: center;">
            <div>
                <span class="badge badge-code" style="color: #4338ca; margin-bottom: 0.75rem;">FASILITAS RESMI</span>
                <h2 style="font-size: 1.65rem; font-weight: 800; color: var(--text-primary); margin-bottom: 1rem;">
                    Tempat Uji Kompetensi (TUK) Mandiri
                </h2>
                <p style="color: var(--text-secondary); font-size: 0.925rem; line-height: 1.6; margin-bottom: 1.5rem;">
                    Dilengkapi dengan laboratorium komputer modern, konektivitas berkecepatan tinggi, serta sarana pengujian standar BNSP untuk memastikan proses asesmen berlangsung optimal dan nyaman.
                </p>
                <div style="display: flex; flex-direction: column; gap: 0.5rem; font-size: 0.85rem; color: var(--text-muted);">
                    <div>📍 <b>Lokasi:</b> Gedung ICT Center, Kampus Darussalam, Banda Aceh</div>
                    <div>🕒 <b>Jadwal:</b> Senin - Jumat (08.00 - 17.00 WIB)</div>
                    <div>✉️ <b>Email:</b> lsp@usk.ac.id | 📞 <b>Telp:</b> (0651) 7553205</div>
                </div>
            </div>
            <div style="background: #f8fafc; border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 2rem; text-align: center;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo LSP" style="width: 80px; height: 80px; object-fit: cover; border-radius: 12px; margin-bottom: 1rem; background: #000; box-shadow: 0 4px 15px rgba(0,0,0,0.15);">
                <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">TUK Lab Komputer USK</h3>
                <p style="font-size: 0.825rem; color: var(--text-muted); margin-bottom: 1.5rem;">
                    Fasilitas terakreditasi dengan perangkat kerja terstandar industri.
                </p>
                <a href="{{ route('lsp.login') }}" class="btn btn-primary" style="font-size: 0.85rem; padding: 0.6rem 1.25rem;">
                    Masuk Portal Admin &rarr;
                </a>
            </div>
        </div>
    </section>

    <!-- Call to Action Banner -->
    <div style="padding: 0 1.5rem;">
        <div class="cta-banner">
            <h2 style="font-size: 2rem; font-weight: 800; letter-spacing: -0.03em; margin-bottom: 1rem;">
                Tingkatkan Kredibilitas Karir dengan Sertifikasi Profesi
            </h2>
            <p style="font-size: 1rem; opacity: 0.85; max-width: 600px; margin: 0 auto 2rem; line-height: 1.6;">
                Dapatkan pengakuan resmi atas keterampilan teknis Anda melalui pengujian kompetensi standar BNSP.
            </p>
            <div style="display: flex; justify-content: center; gap: 1rem; flex-wrap: wrap;">
                <a href="{{ route('lsp.login') }}" class="btn" style="background: #ffffff; color: #1e1b4b; font-weight: 700; padding: 0.75rem 1.75rem;">
                    Login Administrator LSP
                </a>
                <a href="#schemes" class="btn" style="background: rgba(255, 255, 255, 0.15); color: #ffffff; border: 1px solid rgba(255, 255, 255, 0.3); padding: 0.75rem 1.75rem;">
                    Eksplorasi Skema
                </a>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="landing-footer">
        <div style="max-width: 1200px; margin: 0 auto; display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1.5rem;">
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <img src="{{ asset('images/logo.png') }}" alt="Logo LSP" style="width: 32px; height: 32px; border-radius: 6px; object-fit: cover; background: #000;">
                <div>
                    <div style="font-weight: 700; color: var(--text-primary);">LSP CERT USK</div>
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Lembaga Sertifikasi Profesi &copy; {{ date('Y') }}</div>
                </div>
            </div>
            <div style="display: flex; gap: 1.5rem; font-size: 0.8rem;">
                <a href="{{ route('lsp.login') }}" style="color: var(--text-muted); text-decoration: none;">Login Admin</a>
                <a href="#schemes" style="color: var(--text-muted); text-decoration: none;">Skema</a>
                <a href="#features" style="color: var(--text-muted); text-decoration: none;">Keunggulan</a>
                <a href="#tuk" style="color: var(--text-muted); text-decoration: none;">TUK</a>
            </div>
        </div>
    </footer>
</body>
</html>
