@extends('layouts.astro')

@section('title', 'Dashboard - LSP System')

@section('content')
<!-- Header Halaman Dashboard -->
<div class="page-header">
    <div>
        <h2 class="page-title">Overview Ringkasan</h2>
        <p class="page-desc">Monitoring statistik peserta sertifikasi dan master skema aktif LSP.</p>
    </div>
    <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
        <a href="{{ route('lsp.report') }}" class="btn btn-secondary" style="color: #4338ca; border-color: #c7d2fe; background: #eef2ff;">
            <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.72 13.829c-.24-1.076-.672-2.03-1.272-2.829H4.5v-3.75a3 3 0 013-3h9a3 3 0 013 3v3.75h-.948c-.6 0-1.032.953-1.272 2.829m-10.56 0A5.992 5.992 0 006 18v2.25h12V18c0-1.61-.634-3.074-1.68-4.171m-9.6 0h9.6M6.75 6.75h10.5" />
            </svg>
            Print Laporan USK
        </a>
        <a href="{{ route('lsp.schemes') }}" class="btn btn-secondary">
            <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
            </svg>
            Skema
        </a>
        <a href="{{ route('lsp.participants') }}" class="btn btn-primary">
            <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
            </svg>
            Peserta Baru
        </a>
    </div>
</div>

<!-- Kartu Metrik Statistik Utama -->
<div class="stat-grid">
    <!-- Total Peserta -->
    <div class="stat-card">
        <div>
            <div class="stat-label">Total Peserta</div>
            <div class="stat-metric">{{ $totalParticipants }}</div>
        </div>
        <div class="stat-icon-wrap" style="background: #eef2ff; color: #4f46e5;">
            <svg style="width: 22px; height: 22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19.128a9.38 9.38 0 002.625.372 9.337 9.337 0 004.121-.952 4.125 4.125 0 00-7.533-2.493M15 19.128v-.003c0-1.113-.285-2.16-.786-3.07M15 19.128v.106A12.318 12.318 0 018.624 21c-2.331 0-4.512-.645-6.374-1.766l-.001-.109a6.375 6.375 0 0111.964-3.07M12 6.375a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zm8.25 2.25a2.625 2.625 0 11-5.25 0 2.625 2.625 0 015.25 0z" />
            </svg>
        </div>
    </div>

    <!-- Peserta Kompeten -->
    <div class="stat-card">
        <div>
            <div class="stat-label" style="color: var(--accent-emerald);">Peserta Kompeten</div>
            <div class="stat-metric" style="color: var(--accent-emerald);">{{ $totalKompeten }}</div>
        </div>
        <div class="stat-icon-wrap" style="background: var(--accent-emerald-bg); color: var(--accent-emerald);">
            <svg style="width: 22px; height: 22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12c0 1.268-.63 2.39-1.593 3.068a3.745 3.745 0 01-1.043 3.296 3.745 3.745 0 01-3.296 1.043A3.745 3.745 0 0112 21c-1.268 0-2.39-.63-3.068-1.593a3.746 3.746 0 01-3.296-1.043 3.745 3.745 0 01-1.043-3.296A3.745 3.745 0 013 12c0-1.268.63-2.39 1.593-3.068a3.745 3.745 0 011.043-3.296 3.746 3.746 0 013.296-1.043A3.746 3.746 0 0112 3c1.268 0 2.39.63 3.068 1.593a3.746 3.746 0 013.296 1.043 3.746 3.746 0 011.043 3.296A3.745 3.745 0 0121 12z" />
            </svg>
        </div>
    </div>

    <!-- Belum Kompeten -->
    <div class="stat-card">
        <div>
            <div class="stat-label" style="color: var(--accent-amber);">Belum Kompeten</div>
            <div class="stat-metric" style="color: var(--accent-amber);">{{ $totalBelum }}</div>
        </div>
        <div class="stat-icon-wrap" style="background: var(--accent-amber-bg); color: var(--accent-amber);">
            <svg style="width: 22px; height: 22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
        </div>
    </div>

    <!-- Skema Aktif -->
    <div class="stat-card">
        <div>
            <div class="stat-label" style="color: #7c3aed;">Skema Standar</div>
            <div class="stat-metric" style="color: #7c3aed;">{{ $totalSchemes }}</div>
        </div>
        <div class="stat-icon-wrap" style="background: #f5f3ff; color: #7c3aed;">
            <svg style="width: 22px; height: 22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19.5 14.25v-2.625a3.375 3.375 0 00-3.375-3.375h-1.5A1.125 1.125 0 0113.5 7.125v-1.5a3.375 3.375 0 00-3.375-3.375H8.25m0 12.75h7.5m-7.5 3H12M10.5 2.25H5.625c-.621 0-1.125.504-1.125 1.125v17.25c0 .621.504 1.125 1.125 1.125h12.75c.621 0 1.125-.504 1.125-1.125V11.25a9 9 0 00-9-9z" />
            </svg>
        </div>
    </div>
</div>

<!-- Tabel Peserta Terdaftar Terkini -->
<div class="table-container">
    <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center; background: #ffffff;">
        <div>
            <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary);">Peserta Terdaftar Terkini</h3>
            <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.15rem;">Daftar 5 asesi yang baru saja didaftarkan ke sistem.</p>
        </div>
        <a href="{{ route('lsp.participants') }}" class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.4rem 0.85rem;">
            Lihat Semua Peserta &rarr;
        </a>
    </div>

    <table class="astro-table">
        <thead>
            <tr>
                <th>No. Registrasi</th>
                <th>Nama Peserta</th>
                <th>Skema Sertifikasi</th>
                <th>Status Asesmen</th>
                <th>Tanggal Daftar</th>
            </tr>
        </thead>
        <tbody>
            @forelse($recentParticipants as $p)
                <tr>
                    <td>
                        <span class="badge badge-code">{{ $p->registration_number }}</span>
                    </td>
                    <td>
                        <div style="display: flex; align-items: center; gap: 0.75rem;">
                            <div style="width: 32px; height: 32px; border-radius: 50%; background: #e0e7ff; color: #4338ca; display: grid; place-items: center; font-size: 0.8rem; font-weight: 700;">
                                {{ strtoupper(substr($p->full_name, 0, 1)) }}
                            </div>
                            <div>
                                <div style="font-weight: 600; color: var(--text-primary);">{{ $p->full_name }}</div>
                                <div style="font-size: 0.75rem; color: var(--text-muted);">{{ $p->email }}</div>
                            </div>
                        </div>
                    </td>
                    <td>
                        <span style="font-size: 0.85rem; font-weight: 500; color: var(--text-secondary);">
                            {{ $p->scheme->scheme_name ?? '-' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge {{ str_contains($p->status, 'Kompeten') && !str_contains($p->status, 'Belum') ? 'badge-kompeten' : 'badge-belum' }}">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: currentColor;"></span>
                            {{ $p->status }}
                        </span>
                    </td>
                    <td style="font-size: 0.825rem; color: var(--text-muted); font-family: var(--font-mono);">
                        {{ $p->created_at->format('d M Y, H:i') }}
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 3rem 1.5rem;">
                        <div style="font-size: 1.75rem; margin-bottom: 0.5rem; opacity: 0.5;">📋</div>
                        <div style="font-weight: 600;">Belum ada peserta terdaftar</div>
                        <div style="font-size: 0.8rem; margin-top: 0.25rem;">Klik tombol "Peserta Baru" untuk mulai menambahkan data.</div>
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>
</div>
@endsection
