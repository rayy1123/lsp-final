@extends('layouts.astro')

@section('title', (Auth::user()->isAdmin() ? 'Dashboard Admin' : 'Portal Status Asesi') . ' - LSP System')

@section('content')
@if(Auth::user()->isAdmin())
    <!-- ========================================================================= -->
    <!-- DASHBOARD KHUSUS ADMINISTRATOR LSP                                       -->
    <!-- ========================================================================= -->

    <!-- Header Halaman Dashboard Admin -->
    <div class="page-header">
        <div>
            <h2 class="page-title">Overview Administrator</h2>
            <p class="page-desc">Monitoring statistik peserta sertifikasi, verifikasi tugas ujian, dan master skema aktif LSP.</p>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <button class="btn btn-secondary" onclick="openCreateTaskModal()">
                <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.5v15m7.5-7.5h-15" />
                </svg>
                Tambah Unit Tugas
            </button>
            <a href="{{ route('lsp.report') }}" class="btn btn-secondary" style="color: #4338ca; border-color: #c7d2fe; background: #eef2ff;">
                <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.72 13.829c-.24-1.076-.672-2.03-1.272-2.829H4.5v-3.75a3 3 0 013-3h9a3 3 0 013 3v3.75h-.948c-.6 0-1.032.953-1.272 2.829m-10.56 0A5.992 5.992 0 006 18v2.25h12V18c0-1.61-.634-3.074-1.68-4.171m-9.6 0h9.6M6.75 6.75h10.5" />
                </svg>
                Print Laporan USK
            </a>
            <a href="{{ route('lsp.participants') }}" class="btn btn-primary">
                <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 7.5v3m0 0v3m0-3h3m-3 0h-3m-2.25-4.125a3.375 3.375 0 11-6.75 0 3.375 3.375 0 016.75 0zM3 19.235v-.11a6.375 6.375 0 0112.75 0v.109A12.318 12.318 0 019.374 21c-2.331 0-4.512-.645-6.374-1.766z" />
                </svg>
                Daftarkan Peserta
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

    <!-- PANEL 1: PENYELESAIAN TUGAS & VERIFIKASI ASESOR -->
    <div class="table-container" style="margin-bottom: 2rem;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center; background: #ffffff;">
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary);">Penyelesaian Tugas &amp; Verifikasi Asesor</h3>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.15rem;">Periksa bukti pengerjaan tugas ujian asesi dan tentukan status kelulusan (Diterima / Ditolak).</p>
            </div>
            <span class="badge" style="background: #eef2ff; color: #4338ca; font-weight: 700;">
                {{ $taskSubmissions->count() }} Submisi
            </span>
        </div>

        <table class="astro-table">
            <thead>
                <tr>
                    <th>Asesi (Peserta)</th>
                    <th>Skema &amp; Kode Tugas</th>
                    <th>Bukti Pengerjaan Tugas</th>
                    <th style="width: 170px;">Status Penilaian</th>
                    <th style="width: 150px; text-align: right;">Aksi Asesor</th>
                </tr>
            </thead>
            <tbody>
                @forelse($taskSubmissions as $sub)
                    <tr>
                        <td>
                            <div style="font-weight: 700; color: var(--text-primary);">
                                {{ $sub->participant->full_name ?? ($sub->user->name ?? 'Asesi') }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">
                                {{ $sub->participant->registration_number ?? '-' }} &bull; {{ $sub->participant->email ?? $sub->user->email }}
                            </div>
                        </td>
                        <td>
                            <span class="badge badge-code" style="color: #4338ca;">{{ $sub->task->task_code ?? 'TASK' }}</span>
                            <div style="font-size: 0.825rem; font-weight: 600; color: var(--text-primary); margin-top: 0.25rem;">
                                {{ $sub->task->title ?? '-' }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">
                                {{ $sub->task->scheme->scheme_name ?? '-' }}
                            </div>
                        </td>
                        <td>
                            <div style="font-size: 0.825rem; color: var(--text-secondary); max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $sub->submission_content }}
                            </div>
                            <button type="button" class="btn btn-secondary" style="padding: 0.2rem 0.55rem; font-size: 0.75rem; margin-top: 0.35rem;" onclick='openViewSubmissionModal(@json($sub))'>
                                Lihat Bukti Lengkap
                            </button>
                        </td>
                        <td>
                            @if($sub->status === 'Diterima')
                                <span class="badge badge-kompeten">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: currentColor;"></span>
                                    Diterima
                                </span>
                            @elseif($sub->status === 'Ditolak')
                                <span class="badge badge-belum">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: currentColor;"></span>
                                    Ditolak
                                </span>
                            @else
                                <span class="badge" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                                    Menunggu Peninjauan
                                </span>
                            @endif

                            @if($sub->admin_notes)
                                <div style="font-size: 0.725rem; color: var(--text-muted); margin-top: 0.25rem; font-style: italic;">
                                    Catatan: {{ Str::limit($sub->admin_notes, 35) }}
                                </div>
                            @endif
                        </td>
                        <td style="text-align: right;">
                            <button class="btn btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;" onclick='openReviewModal(@json($sub))'>
                                Periksa Tugas
                            </button>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2.5rem 1.5rem;">
                            <div style="font-weight: 600;">Belum ada submisi tugas yang dikumpulkan</div>
                            <div style="font-size: 0.8rem; margin-top: 0.25rem;">Asesi yang mengerjakan tugas ujian akan muncul di sini untuk diperiksa.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PANEL 2: MANAJEMEN UNIT TUGAS / SOAL UJIAN SKEMA -->
    <div class="table-container" style="margin-bottom: 2rem;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center; background: #ffffff;">
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary);">Master Unit Tugas &amp; Soal Ujian Skema</h3>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.15rem;">Daftar unit kompetensi dan petunjuk soal ujian yang wajib diselesaikan asesi.</p>
            </div>
            <button class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.4rem 0.85rem;" onclick="openCreateTaskModal()">
                + Tambah Soal Ujian
            </button>
        </div>

        <table class="astro-table">
            <thead>
                <tr>
                    <th style="width: 140px;">Kode Tugas</th>
                    <th>Skema Sertifikasi</th>
                    <th>Judul Unit Ujian &amp; Uraian Petunjuk</th>
                    <th style="width: 100px; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assessmentTasks as $task)
                    <tr>
                        <td>
                            <span class="badge badge-code" style="color: #4338ca;">{{ $task->task_code }}</span>
                        </td>
                        <td>
                            <span style="font-size: 0.85rem; font-weight: 600; color: var(--text-primary);">
                                {{ $task->scheme->scheme_name ?? '-' }}
                            </span>
                            <div style="font-size: 0.75rem; color: var(--text-muted); font-family: var(--font-mono);">
                                {{ $task->scheme->scheme_code ?? '-' }}
                            </div>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: var(--text-primary); font-size: 0.875rem;">{{ $task->title }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.2rem; line-height: 1.45;">{{ $task->description }}</div>
                        </td>
                        <td style="text-align: right;">
                            <form action="{{ route('lsp.tasks.destroy', $task) }}" method="POST" onsubmit="return confirm('Hapus unit tugas ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="btn btn-danger" style="padding: 0.35rem 0.75rem; font-size: 0.775rem;">
                                    Hapus
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 2.5rem 1.5rem;">
                            <div style="font-weight: 600;">Belum ada data unit tugas/soal ujian</div>
                            <div style="font-size: 0.8rem; margin-top: 0.25rem;">Klik "Tambah Unit Tugas" untuk membuat soal ujian baru untuk asesi.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PANEL 3: PESERTA TERDAFTAR TERKINI -->
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
                            <div style="font-weight: 600;">Belum ada peserta terdaftar</div>
                            <div style="font-size: 0.8rem; margin-top: 0.25rem;">Klik tombol "Daftarkan Peserta" untuk mulai menambahkan data.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@else
    <!-- ========================================================================= -->
    <!-- PORTAL KHUSUS USER / ASESI                                               -->
    <!-- ========================================================================= -->

    <!-- Header Halaman Portal Asesi -->
    <div class="page-header">
        <div>
            <h2 class="page-title">Portal Asesi: {{ Auth::user()->name }}</h2>
            <p class="page-desc">Kelola kelengkapan biodata akun, pengerjaan tugas ujian, dan status pendaftaran skema.</p>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <button class="btn btn-primary" onclick="openEditProfileModal()">
                <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
                Edit Biodata Akun
            </button>
            <a href="{{ route('lsp.assessment.result') }}" class="btn btn-secondary" style="color: #4338ca; border-color: #c7d2fe; background: #eef2ff;">
                <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                </svg>
                Lihat Hasil Asesmen &rarr;
            </a>
        </div>
    </div>

    <!-- SEKSI 1: MASTER DATA & KELENGKAPAN AKUN ASESI (EDIT BIODATA) -->
    <div style="background: #ffffff; border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1.75rem; box-shadow: var(--shadow-card); margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 1rem; margin-bottom: 1.25rem;">
            <div>
                <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-primary);">
                    Master Data &amp; Kelengkapan Profil Akun
                </h3>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.15rem;">
                    Kelengkapan data kontak dan domisili Anda untuk verifikasi sertifikasi BNSP.
                </p>
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="text-align: right;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Status Kelengkapan</div>
                    <div style="font-size: 1.05rem; font-weight: 800; color: {{ $profileCompleteness >= 100 ? 'var(--accent-emerald)' : 'var(--accent-amber)' }};">
                        {{ $profileCompleteness }}% Lengkap
                    </div>
                </div>
                <button class="btn btn-secondary" style="padding: 0.4rem 0.85rem; font-size: 0.8rem;" onclick="openEditProfileModal()">
                    Edit Biodata
                </button>
            </div>
        </div>

        <!-- Detail Biodata Aktif -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; margin-bottom: 1.25rem;">
            <div style="background: #f8fafc; padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                <div style="color: var(--text-muted); font-size: 0.725rem; margin-bottom: 0.25rem;">Nama Lengkap</div>
                <div style="font-weight: 700; color: var(--text-primary); font-size: 0.9rem;">{{ Auth::user()->name }}</div>
            </div>
            <div style="background: #f8fafc; padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                <div style="color: var(--text-muted); font-size: 0.725rem; margin-bottom: 0.25rem;">Email Terdaftar</div>
                <div style="font-weight: 600; color: var(--text-primary); font-size: 0.9rem;">{{ Auth::user()->email }}</div>
            </div>
            <div style="background: #f8fafc; padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                <div style="color: var(--text-muted); font-size: 0.725rem; margin-bottom: 0.25rem;">Nomor WhatsApp / HP</div>
                <div style="font-weight: 600; color: var(--text-primary); font-family: var(--font-mono); font-size: 0.9rem;">
                    {{ Auth::user()->phone_number ?: 'Belum diisi' }}
                </div>
            </div>
            <div style="background: #f8fafc; padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                <div style="color: var(--text-muted); font-size: 0.725rem; margin-bottom: 0.25rem;">Alamat Domisili</div>
                <div style="font-weight: 600; color: var(--text-primary); font-size: 0.9rem;">
                    {{ Auth::user()->address ?: ($myParticipant?->address ?: 'Belum diisi') }}
                </div>
            </div>
        </div>

        <!-- Checklist Kelengkapan Master Data -->
        <div style="display: flex; gap: 1.5rem; flex-wrap: wrap; font-size: 0.8rem; background: #f1f5f9; padding: 0.75rem 1rem; border-radius: var(--radius-sm);">
            <div style="display: flex; align-items: center; gap: 0.4rem; color: {{ $profileFields['name'] ? 'var(--accent-emerald)' : 'var(--text-muted)' }}; font-weight: 600;">
                <span>{{ $profileFields['name'] ? '✓' : '○' }}</span> Nama Lengkap
            </div>
            <div style="display: flex; align-items: center; gap: 0.4rem; color: {{ $profileFields['email'] ? 'var(--accent-emerald)' : 'var(--text-muted)' }}; font-weight: 600;">
                <span>{{ $profileFields['email'] ? '✓' : '○' }}</span> Email Akun
            </div>
            <div style="display: flex; align-items: center; gap: 0.4rem; color: {{ $profileFields['phone_number'] ? 'var(--accent-emerald)' : 'var(--accent-amber)' }}; font-weight: 600;">
                <span>{{ $profileFields['phone_number'] ? '✓' : '⚠' }}</span> No. Telepon
            </div>
            <div style="display: flex; align-items: center; gap: 0.4rem; color: {{ $profileFields['address'] ? 'var(--accent-emerald)' : 'var(--accent-amber)' }}; font-weight: 600;">
                <span>{{ $profileFields['address'] ? '✓' : '⚠' }}</span> Domisili
            </div>
        </div>
    </div>

    <!-- SEKSI 2: INFORMASI SKEMA SERTIFIKASI ASESI -->
    @if($myParticipant)
        <div style="background: #ffffff; border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1.75rem 2rem; box-shadow: var(--shadow-card); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 1.25rem; margin-bottom: 1.25rem;">
                <div>
                    <span class="badge badge-code" style="color: #4338ca; font-size: 0.85rem; padding: 0.25rem 0.6rem;">
                        {{ $myParticipant->registration_number }}
                    </span>
                    <h3 style="font-size: 1.45rem; font-weight: 800; color: var(--text-primary); margin-top: 0.5rem;">
                        {{ $myParticipant->scheme->scheme_name ?? 'Skema Belum Terpilih' }}
                    </h3>
                    <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">
                        Kode Skema: <span style="font-family: var(--font-mono); font-weight: 600; color: var(--text-primary);">{{ $myParticipant->scheme->scheme_code ?? '-' }}</span> &bull; Terdaftar sejak {{ $myParticipant->created_at->format('d M Y') }}
                    </p>
                </div>

                <div style="text-align: right;">
                    <a href="{{ route('lsp.assessment.result') }}" class="btn btn-secondary" style="font-size: 0.85rem; padding: 0.5rem 1rem; color: #4338ca; border-color: #c7d2fe; background: #eef2ff;">
                        <span>Hasil Asesmen Dipindahkan ke Menu Khusus</span> &rarr;
                    </a>
                </div>
            </div>

            <div style="font-size: 0.825rem; color: var(--text-secondary); background: #f8fafc; border: 1px solid var(--border-subtle); padding: 0.85rem 1.25rem; border-radius: var(--radius-sm); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    Status keputusan rekomendasi kelulusan resmi telah dipisahkan ke menu tersendiri demi kepatuhan SOP asesmen.
                </div>
                <a href="{{ route('lsp.assessment.result') }}" style="color: #4338ca; font-weight: 700; text-decoration: none;">
                    Buka Halaman Hasil Asesmen &rarr;
                </a>
            </div>
        </div>

        <!-- SEKSI 3: UNIT TUGAS & UJIAN ASESMEN PRAKTIK ASESI -->
        <div id="tasksSection" style="background: #ffffff; border: 1px solid var(--border-subtle); border-radius: var(--radius-md); box-shadow: var(--shadow-card); overflow: hidden; margin-bottom: 2rem;">
            <div style="padding: 1.25rem 1.75rem; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center; background: #ffffff;">
                <div>
                    <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary);">Unit Tugas &amp; Ujian Asesmen Praktik</h3>
                    <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.15rem;">Selesaikan seluruh tugas soal ujian di bawah ini dan kumpulkan tautan/bukti jawaban untuk diverifikasi asesor.</p>
                </div>
                <span class="badge" style="background: #eef2ff; color: #4338ca; font-weight: 700;">
                    {{ $schemeTasks->count() }} Unit Tugas
                </span>
            </div>

            <table class="astro-table">
                <thead>
                    <tr>
                        <th style="width: 140px;">Kode Tugas</th>
                        <th>Judul &amp; Petunjuk Ujian</th>
                        <th style="width: 170px;">Status Submisi</th>
                        <th style="width: 170px; text-align: right;">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($schemeTasks as $task)
                        @php
                            $sub = $task->submissions->first();
                        @endphp
                        <tr>
                            <td>
                                <span class="badge badge-code" style="color: #4338ca;">{{ $task->task_code }}</span>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--text-primary); font-size: 0.9rem;">{{ $task->title }}</div>
                                <div style="font-size: 0.825rem; color: var(--text-muted); margin-top: 0.25rem; line-height: 1.45;">{{ $task->description }}</div>
                                @if($sub && $sub->admin_notes)
                                    <div style="margin-top: 0.5rem; background: #fff1f2; border: 1px solid #fecdd3; padding: 0.5rem 0.75rem; border-radius: var(--radius-sm); font-size: 0.775rem; color: #be123c;">
                                        <b>Catatan Asesor:</b> {{ $sub->admin_notes }}
                                    </div>
                                @endif
                            </td>
                            <td>
                                @if(!$sub)
                                    <span class="badge" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;">
                                        Belum Dikumpulkan
                                    </span>
                                @elseif($sub->status === 'Diterima')
                                    <span class="badge badge-kompeten">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: currentColor;"></span>
                                        Diterima
                                    </span>
                                @elseif($sub->status === 'Ditolak')
                                    <span class="badge badge-belum">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: currentColor;"></span>
                                        Ditolak
                                    </span>
                                @else
                                    <span class="badge" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                                        Menunggu Peninjauan
                                    </span>
                                @endif
                            </td>
                            <td style="text-align: right;">
                                <button class="btn btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;" onclick='openSubmitTaskModal({{ $task->id }}, "{{ addslashes($task->title) }}", "{{ addslashes($sub->submission_content ?? '') }}")'>
                                    {{ $sub ? 'Update Jawaban' : 'Kumpulkan Tugas' }}
                                </button>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 3rem 1.5rem;">
                                <div style="font-weight: 600;">Belum ada unit tugas ujian untuk skema ini</div>
                                <div style="font-size: 0.8rem; margin-top: 0.25rem;">Administrator belum menambahkan soal tugas untuk skema Anda.</div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

    @else
        <!-- Box Notifikasi Belum Memilih Skema -->
        <div style="background: #eef2ff; border: 1px solid #c7d2fe; border-radius: var(--radius-md); padding: 2rem; margin-bottom: 2rem;">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: #3730a3; margin-bottom: 0.5rem;">
                Anda Belum Terdaftar Pada Skema Sertifikasi
            </h3>
            <p style="font-size: 0.875rem; color: #4338ca; margin-bottom: 1.25rem; line-height: 1.5;">
                Pilih salah satu skema sertifikasi di bawah ini untuk memulai pendaftaran uji kompetensi asesi Anda.
            </p>

            <form action="{{ route('lsp.apply.scheme') }}" method="POST" style="max-width: 480px; display: flex; gap: 0.5rem;">
                @csrf
                <select name="scheme_id" class="form-select" required>
                    <option value="">-- Pilih Skema Sertifikasi --</option>
                    @foreach($allSchemes as $s)
                        <option value="{{ $s->id }}">{{ $s->scheme_code }} - {{ $s->scheme_name }}</option>
                    @endforeach
                </select>
                <button type="submit" class="btn btn-primary" style="white-space: nowrap;">
                    Daftar Skema
                </button>
            </form>
        </div>
    @endif

    <!-- SEKSI 4: KATALOG SKEMA SERTIFIKASI YANG TERSEDIA -->
    <div style="margin-bottom: 1rem;">
        <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">Katalog Skema Sertifikasi LSP</h3>
        <p style="font-size: 0.825rem; color: var(--text-muted);">Skema standar SKKNI aktif yang dapat Anda pilih.</p>
    </div>

    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: 1.25rem;">
        @foreach($allSchemes as $s)
            <div style="background: #ffffff; border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1.5rem; display: flex; flex-direction: column; justify-content: space-between; box-shadow: var(--shadow-sm);">
                <div>
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.75rem;">
                        <span class="badge badge-code" style="color: #4338ca;">{{ $s->scheme_code }}</span>
                        <span style="font-size: 0.75rem; color: var(--accent-emerald); font-weight: 600;">✓ Terakreditasi BNSP</span>
                    </div>
                    <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                        {{ $s->scheme_name }}
                    </h4>
                    <p style="font-size: 0.825rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem;">
                        {{ $s->description ?: 'Standar okupasi keahlian nasional resmi.' }}
                    </p>
                </div>
                <form action="{{ route('lsp.apply.scheme') }}" method="POST">
                    @csrf
                    <input type="hidden" name="scheme_id" value="{{ $s->id }}">
                    <button type="submit" class="btn btn-secondary" style="width: 100%; font-size: 0.825rem;" {{ ($myParticipant && $myParticipant->scheme_id == $s->id) ? 'disabled' : '' }}>
                        {{ ($myParticipant && $myParticipant->scheme_id == $s->id) ? '✓ Skema Aktif Anda' : 'Pilih / Ganti ke Skema Ini' }}
                    </button>
                </form>
            </div>
        @endforeach
    </div>
@endif

<!-- ========================================================================= -->
<!-- MODAL DIALOGS                                                             -->
<!-- ========================================================================= -->

@if(!Auth::user()->isAdmin())
    <!-- MODAL EDIT BIODATA ASESI -->
    <div class="modal-overlay" id="editProfileModal">
        <div class="modal-box" style="max-width: 520px;">
            <h3 class="modal-title">Edit Master Data &amp; Biodata Asesi</h3>
            <form action="{{ route('lsp.profile.update') }}" method="POST">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Nama Lengkap (Sesuai KTP)</label>
                    <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}" required maxlength="50" placeholder="Nama lengkap...">
                    <small style="color: var(--text-muted); font-size: 0.725rem;">Maksimal 50 karakter, huruf dan tanda kutip saja.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Email Akun (Terkunci)</label>
                    <input type="email" class="form-control" value="{{ Auth::user()->email }}" disabled style="background: #f1f5f9; color: #64748b;">
                </div>

                <div class="form-group">
                    <label class="form-label">Password Baru (Opsional)</label>
                    <input type="password" name="password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password" minlength="6">
                    <small style="color: var(--text-muted); font-size: 0.725rem;">Minimal 6 karakter. Isi hanya jika ingin memperbarui kata sandi login Anda.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor WhatsApp / HP Aktif</label>
                    <input type="text" name="phone_number" class="form-control" value="{{ Auth::user()->phone_number }}" required maxlength="13" placeholder="08xxxxxxxxxx">
                    <small style="color: var(--text-muted); font-size: 0.725rem;">Maksimal 13 angka, digunakan untuk konfirmasi jadwal uji.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Domisili Lengkap</label>
                    <textarea name="address" class="form-control" rows="3" placeholder="Alamat domisili lengkap...">{{ Auth::user()->address ?: ($myParticipant?->address ?? '') }}</textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editProfileModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL PENGUMPULAN TUGAS ASESI -->
    <div class="modal-overlay" id="submitTaskModal">
        <div class="modal-box" style="max-width: 560px;">
            <h3 class="modal-title" id="submitTaskTitle">Kumpulkan Bukti Tugas Ujian</h3>
            <form id="submitTaskForm" method="POST" action="">
                @csrf

                <div class="form-group">
                    <label class="form-label">Bukti Jawaban / Tautan Repository / Dokumen Portofolio</label>
                    <textarea name="submission_content" id="submissionContentInput" class="form-control" rows="6" placeholder="Tuliskan tautan GitHub, link Google Drive, atau uraian penyelesaian tugas praktikum Anda di sini..." required maxlength="2000"></textarea>
                    <small style="color: var(--text-muted); font-size: 0.725rem;">Pastikan tautan dapat diakses publik atau asesor uji kompetensi.</small>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('submitTaskModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Kirimkan Bukti Tugas</button>
                </div>
            </form>
        </div>
    </div>
@endif

@if(Auth::user()->isAdmin())
    <!-- MODAL TAMBAH UNIT TUGAS UJIAN (ADMIN) -->
    <div class="modal-overlay" id="createTaskModal">
        <div class="modal-box" style="max-width: 560px;">
            <h3 class="modal-title">Tambah Unit Tugas / Soal Ujian Skema</h3>
            <form action="{{ route('lsp.tasks.store') }}" method="POST">
                @csrf

                <div class="form-group">
                    <label class="form-label">Pilih Skema Sertifikasi</label>
                    <select name="scheme_id" class="form-select" required>
                        <option value="">-- Pilih Skema --</option>
                        @foreach($allSchemes as $s)
                            <option value="{{ $s->id }}">{{ $s->scheme_code }} - {{ $s->scheme_name }}</option>
                        @endforeach
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Kode Unit Tugas</label>
                    <input type="text" name="task_code" class="form-control" placeholder="Contoh: TSK-JWD-01" required maxlength="50">
                </div>

                <div class="form-group">
                    <label class="form-label">Judul Unit Tugas Ujian</label>
                    <input type="text" name="title" class="form-control" placeholder="Judul unit soal/tugas ujian..." required maxlength="255">
                </div>

                <div class="form-group">
                    <label class="form-label">Petunjuk &amp; Uraian Soal Ujian</label>
                    <textarea name="description" class="form-control" rows="4" placeholder="Jelaskan petunjuk teknis pengerjaan tugas ujian praktik yang harus dipenuhi oleh asesi..." required></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('createTaskModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Unit Tugas</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL PERIKSA / REVIEW SUBMISI TUGAS (ADMIN) -->
    <div class="modal-overlay" id="reviewSubmissionModal">
        <div class="modal-box" style="max-width: 540px;">
            <h3 class="modal-title">Pemeriksaan Submisi Tugas Asesi</h3>
            <form id="reviewSubmissionForm" method="POST" action="">
                @csrf

                <div style="background: #f8fafc; border: 1px solid var(--border-subtle); padding: 0.85rem; border-radius: var(--radius-sm); margin-bottom: 1rem;">
                    <div style="font-size: 0.75rem; color: var(--text-muted);">Asesi:</div>
                    <div id="reviewAsesiName" style="font-weight: 700; color: var(--text-primary);"></div>
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.35rem;">Unit Tugas:</div>
                    <div id="reviewTaskTitle" style="font-size: 0.85rem; color: #4338ca; font-weight: 600;"></div>
                </div>

                <div class="form-group">
                    <label class="form-label">Keputusan Penilaian Asesor</label>
                    <select name="status" id="reviewStatusSelect" class="form-select" required>
                        <option value="Diterima">Diterima (Lulus Unit Tugas)</option>
                        <option value="Ditolak">Ditolak (Perlu Revisi / Belum Memenuhi)</option>
                    </select>
                </div>

                <div class="form-group">
                    <label class="form-label">Catatan Review Asesor / Umpan Balik</label>
                    <textarea name="admin_notes" id="reviewNotesInput" class="form-control" rows="3" placeholder="Berikan catatan umpan balik mengenai bukti tugas yang dikumpulkan (opsional)..."></textarea>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('reviewSubmissionModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Hasil Penilaian</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL LIHAT DETAIL BUKTI SUBMISI (ADMIN) -->
    <div class="modal-overlay" id="viewSubmissionModal">
        <div class="modal-box" style="max-width: 600px;">
            <h3 class="modal-title">Detail Bukti Pengerjaan Tugas</h3>
            <div style="margin-bottom: 1rem;">
                <div style="font-size: 0.75rem; color: var(--text-muted);">Nama Asesi:</div>
                <div id="viewAsesiName" style="font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;"></div>

                <div style="font-size: 0.75rem; color: var(--text-muted);">Unit Tugas:</div>
                <div id="viewTaskTitle" style="font-weight: 600; color: #4338ca; margin-bottom: 0.75rem;"></div>

                <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Bukti / Jawaban Lengkap:</div>
                <div id="viewContentText" style="background: #f8fafc; border: 1px solid var(--border-subtle); padding: 1rem; border-radius: var(--radius-sm); font-size: 0.85rem; color: var(--text-primary); white-space: pre-wrap; word-break: break-word; max-height: 250px; overflow-y: auto; font-family: var(--font-mono);"></div>
            </div>
            <div style="display: flex; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('viewSubmissionModal')">Tutup</button>
            </div>
        </div>
    </div>
@endif

@push('scripts')
<script>
    function closeModal(id) {
        const el = document.getElementById(id);
        if (el) el.classList.remove('active');
    }

    // Modal User
    function openEditProfileModal() {
        const el = document.getElementById('editProfileModal');
        if (el) el.classList.add('active');
    }

    function openSubmitTaskModal(taskId, title, content) {
        const el = document.getElementById('submitTaskModal');
        const form = document.getElementById('submitTaskForm');
        const titleEl = document.getElementById('submitTaskTitle');
        const contentEl = document.getElementById('submissionContentInput');

        if (form) form.action = `/lsp/tasks/${taskId}/submit`;
        if (titleEl) titleEl.innerText = `Kumpulkan Bukti: ${title}`;
        if (contentEl) contentEl.value = content || '';
        if (el) el.classList.add('active');
    }

    // Modal Admin
    function openCreateTaskModal() {
        const el = document.getElementById('createTaskModal');
        if (el) el.classList.add('active');
    }

    function openReviewModal(sub) {
        const el = document.getElementById('reviewSubmissionModal');
        const form = document.getElementById('reviewSubmissionForm');
        if (form) form.action = `/lsp/submissions/${sub.id}/review`;

        const asesiName = sub.participant ? sub.participant.full_name : (sub.user ? sub.user.name : 'Asesi');
        const taskTitle = sub.task ? `${sub.task.task_code} - ${sub.task.title}` : 'Unit Tugas';

        document.getElementById('reviewAsesiName').innerText = asesiName;
        document.getElementById('reviewTaskTitle').innerText = taskTitle;
        document.getElementById('reviewStatusSelect').value = sub.status === 'Ditolak' ? 'Ditolak' : 'Diterima';
        document.getElementById('reviewNotesInput').value = sub.admin_notes || '';

        if (el) el.classList.add('active');
    }

    function openViewSubmissionModal(sub) {
        const el = document.getElementById('viewSubmissionModal');
        const asesiName = sub.participant ? sub.participant.full_name : (sub.user ? sub.user.name : 'Asesi');
        const taskTitle = sub.task ? `${sub.task.task_code} - ${sub.task.title}` : 'Unit Tugas';

        document.getElementById('viewAsesiName').innerText = asesiName;
        document.getElementById('viewTaskTitle').innerText = taskTitle;
        document.getElementById('viewContentText').innerText = sub.submission_content || '-';

        if (el) el.classList.add('active');
    }

    // Overlay click listener
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.remove('active');
        });
    });
</script>
@endpush
@endsection
