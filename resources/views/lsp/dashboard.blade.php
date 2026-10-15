@extends('layouts.astro')

@section('title', (Auth::user()->isAdmin() ? 'Dashboard Admin' : 'Selamat Datang') . ' - LSP System')

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
            <a href="{{ route('lsp.report') }}" class="btn btn-secondary" style="color: var(--accent-primary); border-color: var(--accent-primary-border); background: var(--accent-primary-light);">
                <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6.72 13.829c-.24-1.076-.672-2.03-1.272-2.829H4.5v-3.75a3 3 0 013-3h9a3 3 0 013 3v3.75h-.948c-.6 0-1.032.953-1.272 2.829m-10.56 0A5.992 5.992 0 006 18v2.25h12V18c0-1.61-.634-3.074-1.68-4.171m-9.6 0h9.6M6.75 6.75h10.5" />
                </svg>
                Print Laporan USK
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
            <div class="stat-icon-wrap" style="background: var(--accent-primary-light); color: var(--accent-primary);">
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

        <!-- Menunggu Penilaian (Pending) -->
        <div class="stat-card">
            <div>
                <div class="stat-label" style="color: var(--accent-sky);">Menunggu Penilaian</div>
                <div class="stat-metric" style="color: var(--accent-sky);">{{ $totalPending ?? 0 }}</div>
            </div>
            <div class="stat-icon-wrap" style="background: var(--accent-sky-bg); color: var(--accent-sky);">
                <svg style="width: 22px; height: 22px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
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

    <!-- NOTIFIKASI PERUBAHAN / REVISI TUGAS DARI ASESI (ADMIN) -->
    @php
        $revisedSubmissions = $taskSubmissions->filter(function($s) {
            return $s->is_revised || $s->status === 'Pembaruan / Revisi';
        });
    @endphp

    @if($revisedSubmissions->isNotEmpty())
        <div style="background: #eff6ff; border: 1px solid #bfdbfe; border-left: 4px solid #2563eb; border-radius: var(--radius-sm); padding: 1rem 1.25rem; margin-bottom: 1.5rem; display: flex; align-items: flex-start; gap: 0.85rem;">
            <svg style="width: 20px; height: 20px; color: #2563eb; flex-shrink: 0; margin-top: 0.1rem;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z" />
            </svg>
            <div style="flex: 1;">
                <div style="display: flex; align-items: center; justify-content: space-between; gap: 0.5rem; flex-wrap: wrap;">
                    <div style="font-weight: 700; color: #1e40af; font-size: 0.875rem;">
                        Notifikasi Perubahan Isi Tugas ({{ $revisedSubmissions->count() }} Asesi Memperbarui / Merevisi Jawaban)
                    </div>
                    <span class="badge" style="background: #dbeafe; color: #1d4ed8; font-weight: 700; font-size: 0.725rem;">
                        Perlu Peninjauan Asesor
                    </span>
                </div>
                <div style="font-size: 0.8rem; color: #1e3a8a; margin-top: 0.4rem; line-height: 1.5;">
                    Asesi berikut telah memperbarui konten bukti pengerjaan tugas ujian mereka:
                    <ul style="margin: 0.35rem 0 0 1.25rem; padding: 0;">
                        @foreach($revisedSubmissions->take(5) as $rs)
                            <li>
                                <b>{{ $rs->participant->full_name ?? ($rs->user->name ?? 'Asesi') }}</b>
                                &mdash; Tugas: <code>{{ $rs->task->task_code }}</code> ({{ $rs->task->title ?? '' }})
                                <span style="color: #4b5563; font-size: 0.75rem;">
                                    [Revisi ke-{{ $rs->revision_count > 0 ? $rs->revision_count : 1 }}, diperbarui {{ $rs->updated_at->diffForHumans() }}]
                                </span>
                            </li>
                        @endforeach
                    </ul>
                </div>
            </div>
        </div>
    @endif

    <!-- PANEL 1: PENYELESAIAN TUGAS & VERIFIKASI ASESOR -->
    <div class="table-container" style="margin-bottom: 2rem;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center; background: #ffffff;">
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary);">Penyelesaian Tugas &amp; Verifikasi Asesor</h3>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.15rem;">Periksa bukti pengerjaan tugas ujian asesi dan tentukan status kelulusan (Diterima / Ditolak).</p>
            </div>
            <span class="badge" style="background: var(--accent-primary-light); color: var(--accent-primary); border: 1px solid var(--accent-primary-border); font-weight: 700;">
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
                    <tr style="{{ $sub->is_revised ? 'background: #f8fafc;' : '' }}">
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                                <div style="font-weight: 700; color: var(--text-primary);">
                                    {{ $sub->participant->full_name ?? ($sub->user->name ?? 'Asesi') }}
                                </div>
                                @if($sub->is_revised)
                                    <span class="badge" style="background: #dbeafe; color: #1d4ed8; font-weight: 700; font-size: 0.65rem; padding: 0.15rem 0.4rem;" title="Asesi telah memperbarui isi jawaban tugas">
                                        REVISI KE-{{ $sub->revision_count > 0 ? $sub->revision_count : 1 }}
                                    </span>
                                @endif
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.15rem;">
                                {{ $sub->participant->registration_number ?? '-' }} &bull; {{ $sub->participant->email ?? $sub->user->email }}
                            </div>
                            @if($sub->is_revised)
                                <div style="font-size: 0.7rem; color: #2563eb; font-weight: 600; margin-top: 0.2rem;">
                                    Diperbarui {{ $sub->updated_at->diffForHumans() }}
                                </div>
                            @endif
                        </td>
                        <td>
                            <span class="badge badge-code" style="color: var(--accent-primary);">{{ $sub->task->task_code ?? 'TASK' }}</span>
                            <div style="font-size: 0.825rem; font-weight: 600; color: var(--text-primary); margin-top: 0.25rem;">
                                {{ $sub->task->title ?? '-' }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">
                                {{ $sub->task->scheme->scheme_name ?? '-' }}
                            </div>
                        </td>
                        <td>
                            @php
                                preg_match_all('/https?:\/\/[^\s]+/', $sub->submission_content, $subLinks);
                                $firstLink = $subLinks[0][0] ?? null;
                            @endphp
                            <div style="font-size: 0.825rem; color: var(--text-secondary); max-width: 320px; overflow: hidden; text-overflow: ellipsis; white-space: nowrap;">
                                {{ $sub->submission_content }}
                            </div>

                            <div style="display: flex; flex-wrap: wrap; gap: 0.35rem; margin-top: 0.35rem; align-items: center;">
                                @if($firstLink)
                                    <a href="{{ $firstLink }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 0.725rem; text-decoration: none; color: var(--accent-primary);">
                                        🔗 Buka Link
                                    </a>
                                @endif

                                @if($sub->file_path)
                                    @if($sub->isImage())
                                        <a href="{{ $sub->fileUrl() }}" target="_blank" class="btn btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 0.725rem; text-decoration: none; color: #166534; background: #f0fdf4; border-color: #bbf7d0;">
                                            🖼️ Gambar
                                        </a>
                                    @else
                                        <a href="{{ $sub->fileUrl() }}" target="_blank" download class="btn btn-secondary" style="padding: 0.2rem 0.5rem; font-size: 0.725rem; text-decoration: none; color: #1e40af; background: #eff6ff; border-color: #bfdbfe;">
                                            📎 Berkas
                                        </a>
                                    @endif
                                @endif

                                <button type="button" class="btn btn-secondary" style="padding: 0.2rem 0.55rem; font-size: 0.725rem;" onclick='openViewSubmissionModal(@json($sub))'>
                                    Detail
                                </button>
                            </div>
                        </td>
                        <td>
                            @if($sub->is_revised || $sub->status === 'Pembaruan / Revisi')
                                <span class="badge" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: #2563eb;"></span>
                                    Pembaruan / Revisi
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
                    <th style="width: 130px;">Kode Tugas</th>
                    <th>Skema Sertifikasi</th>
                    <th>Judul Unit Ujian &amp; Uraian Petunjuk</th>
                    <th style="width: 170px;">Batas Waktu (Deadline)</th>
                    <th style="width: 90px; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($assessmentTasks as $task)
                    <tr>
                        <td>
                            <span class="badge badge-code" style="color: var(--accent-primary);">{{ $task->task_code }}</span>
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
                        <td>
                            @if($task->due_date)
                                @php
                                    $isExpired = now()->greaterThan($task->due_date);
                                @endphp
                                <div style="font-size: 0.8rem; font-weight: 600; font-family: var(--font-mono); color: {{ $isExpired ? 'var(--accent-rose)' : 'var(--text-primary)' }};">
                                    {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y, H:i') }}
                                </div>
                                <div style="font-size: 0.725rem; color: {{ $isExpired ? 'var(--accent-rose)' : 'var(--accent-emerald)' }}; margin-top: 0.15rem; font-weight: 600;">
                                    {{ $isExpired ? 'Telah Berakhir' : 'Sisa ' . now()->diffForHumans(\Carbon\Carbon::parse($task->due_date), true) }}
                                </div>
                            @else
                                <span style="font-size: 0.775rem; color: var(--text-muted); font-style: italic;">Tidak Ada Batas Waktu</span>
                            @endif
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
                        <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2.5rem 1.5rem;">
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
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: var(--bg-surface-elevated); color: var(--text-primary); border: 1px solid var(--border-strong); display: grid; place-items: center; font-size: 0.8rem; font-weight: 700; font-family: var(--font-mono);">
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
                            <span class="badge {{ str_contains($p->status, 'Kompeten') && !str_contains($p->status, 'Belum') ? 'badge-kompeten' : (str_contains($p->status, 'Pending') ? 'badge-pending' : 'badge-belum') }}">
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
                            <div style="font-size: 0.8rem; margin-top: 0.25rem;">Asesi yang mendaftar melalui portal pendaftaran akan otomatis tercatat di sini.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- PANEL 4: MASTER DATA AKUN & BIODATA LENGKAP ASESI (ADMIN) -->
    <div class="table-container" style="margin-top: 2rem;">
        <div style="padding: 1.25rem 1.5rem; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center; background: #ffffff; flex-wrap: wrap; gap: 0.75rem;">
            <div>
                <h3 style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                    <span>Master Akun &amp; Biodata Lengkap Asesi</span>
                    <span class="badge badge-code" style="font-size: 0.75rem;">{{ count($allUsers ?? []) }} Akun Terdata</span>
                </h3>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.15rem;">
                    Daftar seluruh akun pengguna sistem, skema sertifikasi yang sedang ditempuh, dan kelengkapan biodata identitas.
                </p>
            </div>
            <div style="display: flex; gap: 0.5rem; align-items: center;">
                <input type="text" id="accountSearchInput" class="form-control" style="font-size: 0.8rem; padding: 0.4rem 0.75rem; width: 260px;" placeholder="Cari nama, email, NIK, skema..." onkeyup="filterAccountsTable()">
            </div>
        </div>

        <table class="astro-table" id="accountsTable">
            <thead>
                <tr>
                    <th style="width: 50px; text-align: center;">No</th>
                    <th style="width: 240px;">Nama &amp; Akun</th>
                    <th style="width: 170px;">NIK &amp; Gender</th>
                    <th style="width: 200px;">Kontak &amp; Domisili</th>
                    <th>Skema yang Ditempuh</th>
                    <th style="width: 130px; text-align: right;">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($allUsers ?? [] as $idx => $u)
                    @php
                        $part = $u->participant;
                        $sch = $part?->scheme;
                        $nik = $u->nik ?: ($part?->nik ?: '-');
                        $gender = $u->gender ?: ($part?->gender ?: '-');
                        $eduType = $u->education_type ?: ($part?->education_type ?: 'Siswa/Pelajar');
                        $academicTitle = $u->academic_title ?: ($part?->academic_title ?: null);
                        $phone = $u->phone_number ?: ($part?->phone_number ?: '-');
                        $address = $u->address ?: ($part?->address ?: '-');
                    @endphp
                    <tr class="account-row" data-search="{{ strtolower($u->name . ' ' . $u->email . ' ' . $nik . ' ' . $eduType . ' ' . ($academicTitle ?? '') . ' ' . ($sch?->scheme_name ?? '') . ' ' . ($sch?->scheme_code ?? '') . ' ' . ($part?->registration_number ?? '')) }}">
                        <td style="text-align: center; font-family: var(--font-mono); font-size: 0.8rem; color: var(--text-muted);">
                            {{ $idx + 1 }}
                        </td>
                        <td>
                            <div style="display: flex; align-items: center; gap: 0.75rem;">
                                <div style="width: 32px; height: 32px; border-radius: 50%; background: {{ $u->isAdmin() ? 'var(--accent-primary-light)' : 'var(--bg-surface-elevated)' }}; color: {{ $u->isAdmin() ? 'var(--accent-primary)' : 'var(--text-primary)' }}; border: 1px solid {{ $u->isAdmin() ? 'var(--accent-primary-border)' : 'var(--border-strong)' }}; display: grid; place-items: center; font-size: 0.8rem; font-weight: 700; font-family: var(--font-mono); flex-shrink: 0;">
                                    {{ strtoupper(substr($u->name, 0, 1)) }}
                                </div>
                                <div style="min-width: 0;">
                                    <div style="font-weight: 600; color: var(--text-primary); display: flex; align-items: center; gap: 0.35rem; flex-wrap: wrap;">
                                        <span>{{ $u->name }}</span>
                                        @if($u->isAdmin())
                                            <span class="badge" style="background: var(--accent-primary-light); color: var(--accent-primary); border: 1px solid var(--accent-primary-border); font-size: 0.65rem; padding: 0.1rem 0.35rem;">ADMIN</span>
                                        @else
                                            <span class="badge badge-code" style="font-size: 0.65rem; padding: 0.1rem 0.35rem;">ASESI</span>
                                        @endif
                                    </div>
                                    <div style="font-size: 0.75rem; color: var(--text-muted); text-overflow: ellipsis; overflow: hidden; white-space: nowrap;">{{ $u->email }}</div>
                                </div>
                            </div>
                        </td>
                        <td>
                            <div style="font-family: var(--font-mono); font-size: 0.825rem; font-weight: 600; color: var(--text-primary);">
                                {{ $nik !== '-' ? $nik : 'Belum diisi' }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.15rem;">
                                {{ $gender }}
                            </div>
                            <div style="margin-top: 0.25rem;">
                                <span class="badge" style="font-size: 0.65rem; padding: 0.1rem 0.35rem; background: #f1f5f9; color: var(--text-secondary); border: 1px solid var(--border-subtle);">
                                    {{ $eduType }}{{ $eduType === 'Mahasiswa' && $academicTitle ? ' (' . $academicTitle . ')' : '' }}
                                </span>
                            </div>
                        </td>
                        <td>
                            <div style="font-family: var(--font-mono); font-size: 0.825rem; color: var(--text-primary);">
                                {{ $phone }}
                            </div>
                            <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.15rem; line-height: 1.35;" title="{{ $address }}">
                                {{ Str::limit($address, 32) }}
                            </div>
                        </td>
                        <td>
                            @if($sch)
                                <div style="font-weight: 700; color: var(--text-primary); font-size: 0.85rem;">
                                    {{ $sch->scheme_name }}
                                </div>
                                <div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.15rem; display: flex; align-items: center; gap: 0.4rem; flex-wrap: wrap;">
                                    <span class="badge badge-code" style="font-size: 0.675rem; color: var(--accent-primary);">{{ $sch->scheme_code }}</span>
                                    <span>&bull;</span>
                                    <span style="font-family: var(--font-mono); font-size: 0.75rem;">{{ $part->registration_number }}</span>
                                </div>
                                <div style="margin-top: 0.35rem;">
                                    <span class="badge {{ str_contains($part->status, 'Kompeten') && !str_contains($part->status, 'Belum') ? 'badge-kompeten' : (str_contains($part->status, 'Pending') ? 'badge-pending' : 'badge-belum') }}" style="font-size: 0.725rem; padding: 0.2rem 0.5rem;">
                                        <span style="width: 5px; height: 5px; border-radius: 50%; background: currentColor;"></span>
                                        {{ $part->status }}
                                    </span>
                                </div>
                            @else
                                <span style="font-size: 0.8rem; color: var(--text-dim); font-style: italic;">
                                    Belum memilih skema
                                </span>
                            @endif
                        </td>
                        <td style="text-align: right; white-space: nowrap;">
                            <div style="display: inline-flex; align-items: center; gap: 0.35rem;">
                                <button type="button" class="btn btn-secondary" style="padding: 0.35rem 0.65rem; font-size: 0.775rem;" onclick='openUserAccountModal(@json($u), @json($part), @json($sch))'>
                                    Detail
                                </button>
                                <button type="button" class="btn btn-secondary" style="padding: 0.35rem 0.65rem; font-size: 0.775rem; color: var(--accent-primary); border-color: var(--accent-primary-border);" onclick='openEditUserModal(@json($u), @json($part))'>
                                    Edit
                                </button>
                                @if($u->id !== Auth::id())
                                    <form action="{{ route('lsp.users.destroy', $u) }}" method="POST" style="margin: 0; display: inline-block;" onsubmit="return confirm('Konfirmasi Hapus: Apakah Anda yakin ingin menghapus akun {{ $u->name }} ({{ $u->email }})?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-danger" style="padding: 0.35rem 0.6rem; font-size: 0.775rem;" title="Hapus Akun Pengguna">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 3rem 1.5rem;">
                            Belum ada akun pengguna terdaftar di sistem.
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
            <h2 class="page-title">Selamat Datang, {{ Auth::user()->name }}</h2>
            <p class="page-desc">Kelola kelengkapan biodata akun, pengerjaan tugas ujian, dan status pendaftaran skema.</p>
        </div>
        <div style="display: flex; gap: 0.75rem; flex-wrap: wrap;">
            <button class="btn btn-primary" onclick="openEditProfileModal()">
                <svg style="width: 16px; height: 16px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16.862 4.487l1.687-1.688a1.875 1.875 0 112.652 2.652L10.582 16.07a4.5 4.5 0 01-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 011.13-1.897l8.932-8.931zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0115.75 21H5.25A2.25 2.25 0 013 18.75V8.25A2.25 2.25 0 015.25 6H10" />
                </svg>
                Edit Biodata Akun
            </button>
            <a href="{{ route('lsp.assessment.result') }}" class="btn btn-secondary" style="color: var(--accent-primary); border-color: var(--accent-primary-border); background: var(--accent-primary-light);">
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
                    Kelengkapan data identitas dan berkas persyaratan Anda untuk verifikasi sertifikasi BNSP.
                </p>
            </div>
            <div style="display: flex; align-items: center; gap: 0.75rem;">
                <div style="text-align: right;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); font-weight: 600;">Status Kelengkapan</div>
                    <div style="font-size: 1.05rem; font-weight: 800; color: {{ $profileCompleteness >= 100 ? 'var(--accent-emerald)' : 'var(--accent-amber)' }};">
                        {{ $profileCompleteness }}% Lengkap
                    </div>
                    <div style="font-size: 0.7rem; color: var(--text-muted); font-weight: 500;">
                        ({{ $uploadedDocsCount }} dari {{ $totalDocsCount }} Berkas Terunggah)
                    </div>
                </div>
                <button class="btn btn-secondary" style="padding: 0.4rem 0.85rem; font-size: 0.8rem;" onclick="openEditProfileModal()">
                    Edit Biodata &amp; Berkas
                </button>
            </div>
        </div>

        <!-- Detail Biodata Aktif -->
        @php
            $eduType = Auth::user()->education_type ?: ($myParticipant?->education_type ?: 'Siswa/Pelajar');
            $academicTitle = Auth::user()->academic_title ?: ($myParticipant?->academic_title ?: null);
        @endphp
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 1rem; margin-bottom: 1.25rem;">
            <div style="background: #f8fafc; padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                <div style="color: var(--text-muted); font-size: 0.725rem; margin-bottom: 0.25rem;">Nama Lengkap</div>
                <div style="font-weight: 700; color: var(--text-primary); font-size: 0.9rem;">{{ Auth::user()->name }}</div>
            </div>
            <div style="background: #f8fafc; padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                <div style="color: var(--text-muted); font-size: 0.725rem; margin-bottom: 0.25rem;">NIK (KTP)</div>
                <div style="font-weight: 600; color: var(--text-primary); font-family: var(--font-mono); font-size: 0.9rem;">
                    {{ Auth::user()->nik ?: ($myParticipant?->nik ?: 'Belum diisi') }}
                </div>
            </div>
            <div style="background: #f8fafc; padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                <div style="color: var(--text-muted); font-size: 0.725rem; margin-bottom: 0.25rem;">Jenis Kelamin</div>
                <div style="font-weight: 600; color: var(--text-primary); font-size: 0.9rem;">
                    {{ Auth::user()->gender ?: ($myParticipant?->gender ?: 'Belum diisi') }}
                </div>
            </div>
            <div style="background: #f8fafc; padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                <div style="color: var(--text-muted); font-size: 0.725rem; margin-bottom: 0.25rem;">Kategori Asesi</div>
                <div style="font-weight: 600; color: var(--text-primary); font-size: 0.9rem;">
                    {{ $eduType }}
                </div>
            </div>
            @if(in_array($eduType, ['Mahasiswa', 'Umum']))
            <div style="background: #f8fafc; padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                <div style="color: var(--text-muted); font-size: 0.725rem; margin-bottom: 0.25rem;">Gelar Akademik / Profesi</div>
                <div style="font-weight: 600; color: var(--accent-primary); font-size: 0.9rem;">
                    {{ $academicTitle ?: 'Belum diisi' }}
                </div>
            </div>
            @endif
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

        <!-- Seksi Dokumen Persyaratan Asesi (6 Dokumen) -->
        @php
            $docsList = [
                ['name' => 'Foto KTP / Kartu Pelajar', 'key' => 'id_card_file', 'file' => Auth::user()->id_card_file ?: ($myParticipant?->id_card_file ?? null)],
                ['name' => 'Rapor Semester 5 / Terakhir', 'key' => 'report_sheet_file', 'file' => Auth::user()->report_sheet_file ?: ($myParticipant?->report_sheet_file ?? null)],
                ['name' => 'Sertifikat Pelatihan / Terkait', 'key' => 'certificate_file', 'file' => Auth::user()->certificate_file ?: ($myParticipant?->certificate_file ?? null)],
                ['name' => 'Ijazah Terakhir / Surat Keterangan', 'key' => 'diploma_file', 'file' => Auth::user()->diploma_file ?: ($myParticipant?->diploma_file ?? null)],
                ['name' => 'Pas Foto Formal (3x4 / 4x6)', 'key' => 'photo_file', 'file' => Auth::user()->photo_file ?: ($myParticipant?->photo_file ?? null)],
                ['name' => 'Portofolio / Dokumen Pendukung', 'key' => 'portfolio_file', 'file' => Auth::user()->portfolio_file ?: ($myParticipant?->portfolio_file ?? null)],
            ];
        @endphp

        <div style="margin-top: 1.25rem; border-top: 1px solid var(--border-subtle); padding-top: 1.25rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.85rem; flex-wrap: wrap; gap: 0.5rem;">
                <div style="font-size: 0.85rem; font-weight: 700; color: var(--text-primary); display: flex; align-items: center; gap: 0.5rem;">
                    <span>📁 Dokumen Persyaratan Sertifikasi</span>
                    <span class="badge {{ $uploadedDocsCount === 6 ? 'badge-kompeten' : 'badge-pending' }}" style="font-size: 0.7rem;">
                        {{ $uploadedDocsCount }} / {{ $totalDocsCount }} Berkas Terunggah
                    </span>
                </div>
                <button type="button" class="btn btn-secondary" style="padding: 0.25rem 0.65rem; font-size: 0.75rem;" onclick="openEditProfileModal()">
                    + Kelola / Unggah Berkas
                </button>
            </div>

            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: 0.65rem;">
                @foreach($docsList as $idx => $d)
                    <div style="background: {{ $d['file'] ? '#f0fdf4' : '#f8fafc' }}; border: 1px solid {{ $d['file'] ? '#bbf7d0' : 'var(--border-subtle)' }}; padding: 0.65rem 0.85rem; border-radius: var(--radius-sm); display: flex; justify-content: space-between; align-items: center; gap: 0.5rem;">
                        <div style="min-width: 0;">
                            <div style="font-size: 0.775rem; font-weight: 600; color: var(--text-primary); white-space: nowrap; overflow: hidden; text-overflow: ellipsis;">
                                {{ $idx + 1 }}. {{ $d['name'] }}
                            </div>
                            <div style="font-size: 0.7rem; color: {{ $d['file'] ? '#15803d' : 'var(--text-muted)' }}; margin-top: 0.1rem;">
                                @if($d['file'])
                                    ✓ Terunggah
                                @else
                                    ○ Belum Diunggah
                                @endif
                            </div>
                        </div>
                        @if($d['file'])
                            <a href="{{ asset($d['file']) }}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="font-size: 0.7rem; padding: 0.2rem 0.5rem; color: #15803d; border-color: #86efac; flex-shrink: 0;">
                                Buka Berkas &rarr;
                            </a>
                        @else
                            <button type="button" class="btn btn-secondary" onclick="openEditProfileModal()" style="font-size: 0.7rem; padding: 0.2rem 0.5rem; color: var(--text-muted); flex-shrink: 0;">
                                Unggah
                            </button>
                        @endif
                    </div>
                @endforeach
            </div>
        </div>

        <!-- Checklist Kelengkapan Master Data & Berkas -->
        <div style="display: flex; gap: 0.75rem 1.25rem; flex-wrap: wrap; font-size: 0.75rem; background: #f1f5f9; padding: 0.75rem 1rem; border-radius: var(--radius-sm); margin-top: 1.25rem;">
            <div style="display: flex; align-items: center; gap: 0.3rem; color: {{ $profileFields['name'] ? 'var(--accent-emerald)' : 'var(--text-muted)' }}; font-weight: 600;">
                <span>{{ $profileFields['name'] ? '✓' : '○' }}</span> Nama
            </div>
            <div style="display: flex; align-items: center; gap: 0.3rem; color: {{ $profileFields['nik'] ? 'var(--accent-emerald)' : 'var(--accent-amber)' }}; font-weight: 600;">
                <span>{{ $profileFields['nik'] ? '✓' : '⚠' }}</span> NIK
            </div>
            <div style="display: flex; align-items: center; gap: 0.3rem; color: {{ $profileFields['gender'] ? 'var(--accent-emerald)' : 'var(--accent-amber)' }}; font-weight: 600;">
                <span>{{ $profileFields['gender'] ? '✓' : '⚠' }}</span> Gender
            </div>
            <div style="display: flex; align-items: center; gap: 0.3rem; color: {{ $profileFields['email'] ? 'var(--accent-emerald)' : 'var(--text-muted)' }}; font-weight: 600;">
                <span>{{ $profileFields['email'] ? '✓' : '○' }}</span> Email
            </div>
            <div style="display: flex; align-items: center; gap: 0.3rem; color: {{ $profileFields['phone_number'] ? 'var(--accent-emerald)' : 'var(--accent-amber)' }}; font-weight: 600;">
                <span>{{ $profileFields['phone_number'] ? '✓' : '⚠' }}</span> No. HP
            </div>
            <div style="display: flex; align-items: center; gap: 0.3rem; color: {{ $profileFields['address'] ? 'var(--accent-emerald)' : 'var(--accent-amber)' }}; font-weight: 600;">
                <span>{{ $profileFields['address'] ? '✓' : '⚠' }}</span> Domisili
            </div>
            <div style="display: flex; align-items: center; gap: 0.3rem; color: {{ $profileFields['id_card_file'] ? 'var(--accent-emerald)' : 'var(--accent-amber)' }}; font-weight: 600;">
                <span>{{ $profileFields['id_card_file'] ? '✓' : '⚠' }}</span> KTP
            </div>
            <div style="display: flex; align-items: center; gap: 0.3rem; color: {{ $profileFields['report_sheet_file'] ? 'var(--accent-emerald)' : 'var(--accent-amber)' }}; font-weight: 600;">
                <span>{{ $profileFields['report_sheet_file'] ? '✓' : '⚠' }}</span> Rapor
            </div>
            <div style="display: flex; align-items: center; gap: 0.3rem; color: {{ $profileFields['certificate_file'] ? 'var(--accent-emerald)' : 'var(--accent-amber)' }}; font-weight: 600;">
                <span>{{ $profileFields['certificate_file'] ? '✓' : '⚠' }}</span> Sertifikat
            </div>
            <div style="display: flex; align-items: center; gap: 0.3rem; color: {{ $profileFields['diploma_file'] ? 'var(--accent-emerald)' : 'var(--accent-amber)' }}; font-weight: 600;">
                <span>{{ $profileFields['diploma_file'] ? '✓' : '⚠' }}</span> Ijazah
            </div>
            <div style="display: flex; align-items: center; gap: 0.3rem; color: {{ $profileFields['photo_file'] ? 'var(--accent-emerald)' : 'var(--accent-amber)' }}; font-weight: 600;">
                <span>{{ $profileFields['photo_file'] ? '✓' : '⚠' }}</span> Pas Foto
            </div>
            <div style="display: flex; align-items: center; gap: 0.3rem; color: {{ $profileFields['portfolio_file'] ? 'var(--accent-emerald)' : 'var(--accent-amber)' }}; font-weight: 600;">
                <span>{{ $profileFields['portfolio_file'] ? '✓' : '⚠' }}</span> Portofolio
            </div>
        </div>
    </div>

    <!-- SEKSI 2: INFORMASI SKEMA SERTIFIKASI ASESI -->
    @if($myParticipant)
        <div style="background: #ffffff; border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 1.75rem 2rem; box-shadow: var(--shadow-card); margin-bottom: 2rem;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 1.25rem; margin-bottom: 1.25rem;">
                <div>
                    <span class="badge badge-code" style="color: var(--accent-primary); font-size: 0.85rem; padding: 0.25rem 0.6rem;">
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
                    <a href="{{ route('lsp.assessment.result') }}" class="btn btn-secondary" style="font-size: 0.85rem; padding: 0.5rem 1rem; color: var(--accent-primary); border-color: var(--accent-primary-border); background: var(--accent-primary-light);">
                        <span>Hasil Asesmen Dipindahkan ke Menu Khusus</span> &rarr;
                    </a>
                </div>
            </div>

            <div style="font-size: 0.825rem; color: var(--text-secondary); background: #f8fafc; border: 1px solid var(--border-subtle); padding: 0.85rem 1.25rem; border-radius: var(--radius-sm); display: flex; justify-content: space-between; align-items: center; flex-wrap: wrap; gap: 0.75rem;">
                <div>
                    Status keputusan rekomendasi kelulusan resmi telah dipisahkan ke menu tersendiri demi kepatuhan SOP asesmen.
                </div>
                <a href="{{ route('lsp.assessment.result') }}" style="color: var(--accent-primary); font-weight: 700; text-decoration: none;">
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
                <span class="badge" style="background: var(--accent-primary-light); color: var(--accent-primary); border: 1px solid var(--accent-primary-border); font-weight: 700;">
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
                            $isExpired = $task->due_date && now()->greaterThan($task->due_date);
                        @endphp
                        <tr>
                            <td>
                                <span class="badge badge-code" style="color: var(--accent-primary);">{{ $task->task_code }}</span>
                            </td>
                            <td>
                                <div style="font-weight: 700; color: var(--text-primary); font-size: 0.9rem;">{{ $task->title }}</div>
                                <div style="font-size: 0.825rem; color: var(--text-muted); margin-top: 0.25rem; line-height: 1.45;">{{ $task->description }}</div>

                                @if($task->due_date)
                                    <div style="margin-top: 0.5rem; display: inline-flex; align-items: center; gap: 0.4rem; font-size: 0.775rem; background: {{ $isExpired ? '#fff1f2' : '#f0fdf4' }}; border: 1px solid {{ $isExpired ? '#fecdd3' : '#bbf7d0' }}; color: {{ $isExpired ? '#be123c' : '#15803d' }}; padding: 0.25rem 0.6rem; border-radius: var(--radius-xs); font-weight: 600;">
                                        <svg style="width: 14px; height: 14px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6h4.5m4.5 0a9 9 0 11-18 0 9 9 0 0118 0z" />
                                        </svg>
                                        <span>Batas Waktu: {{ \Carbon\Carbon::parse($task->due_date)->format('d M Y, H:i') }} WIB ({{ $isExpired ? 'Telah Berakhir' : 'Sisa ' . now()->diffForHumans(\Carbon\Carbon::parse($task->due_date), true) }})</span>
                                    </div>
                                @endif

                                @if($sub && $sub->file_path)
                                    <div style="margin-top: 0.5rem; display: flex; align-items: center; gap: 0.4rem; font-size: 0.75rem;">
                                        @if($sub->isImage())
                                            <a href="{{ $sub->fileUrl() }}" target="_blank" style="display: inline-flex; align-items: center; gap: 0.25rem; color: #166534; font-weight: 600; text-decoration: underline;">
                                                🖼️ Lihat Gambar: {{ $sub->file_name ?: 'Lampiran Tugas' }}
                                            </a>
                                        @else
                                            <a href="{{ $sub->fileUrl() }}" target="_blank" download style="display: inline-flex; align-items: center; gap: 0.25rem; color: #1e40af; font-weight: 600; text-decoration: underline;">
                                                📎 Unduh Berkas: {{ $sub->file_name ?: 'Lampiran Tugas' }}
                                            </a>
                                        @endif
                                    </div>
                                @endif

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
                                @elseif($sub->is_revised || $sub->status === 'Pembaruan / Revisi')
                                    <span class="badge" style="background: #eff6ff; color: #1d4ed8; border: 1px solid #bfdbfe;">
                                        <span style="width: 6px; height: 6px; border-radius: 50%; background: #2563eb;"></span>
                                        Revisi Dikirim (Tinjauan Asesor)
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
                                @if($isExpired)
                                    <span class="badge" style="background: #fff1f2; color: #e11d48; border: 1px solid #fecdd3; padding: 0.35rem 0.6rem; font-size: 0.75rem;">
                                        Waktu Berakhir
                                    </span>
                                @else
                                    <button class="btn btn-primary" style="padding: 0.35rem 0.75rem; font-size: 0.8rem;" onclick='openSubmitTaskModal({{ $task->id }}, "{{ addslashes($task->title) }}", "{{ addslashes($sub->submission_content ?? '') }}", "{{ addslashes($sub->file_name ?? '') }}", "{{ $sub ? $sub->fileUrl() : '' }}")'>
                                        {{ $sub ? 'Update Jawaban / Revisi' : 'Kumpulkan Tugas' }}
                                    </button>
                                @endif
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
        <div style="background: var(--bg-surface-elevated); border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 2rem; margin-bottom: 2rem;">
            <h3 style="font-size: 1.15rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                Anda Belum Terdaftar Pada Skema Sertifikasi
            </h3>
            <p style="font-size: 0.875rem; color: var(--text-secondary); margin-bottom: 1.25rem; line-height: 1.5;">
                Pilih salah satu skema sertifikasi di bawah ini untuk memulai pendaftaran uji kompetensi asesi Anda.
            </p>

            <form action="{{ route('lsp.apply.scheme') }}" method="POST" style="max-width: 480px; display: flex; gap: 0.5rem;" onsubmit="return confirm('Konfirmasi pendaftaran ke skema sertifikasi ini?')">
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
                        <span class="badge badge-code" style="color: var(--accent-primary);">{{ $s->scheme_code }}</span>
                        <span style="font-size: 0.75rem; color: var(--accent-emerald); font-weight: 600;">✓ Terakreditasi BNSP</span>
                    </div>
                    <h4 style="font-size: 1.05rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
                        {{ $s->scheme_name }}
                    </h4>
                    <p style="font-size: 0.825rem; color: var(--text-muted); line-height: 1.5; margin-bottom: 1.25rem;">
                        {{ $s->description ?: 'Standar okupasi keahlian nasional resmi.' }}
                    </p>
                </div>
                <form action="{{ route('lsp.apply.scheme') }}" method="POST" onsubmit="return confirm('Apakah Anda yakin ingin berganti ke skema sertifikasi ini? Data pendaftaran uji kompetensi Anda akan dialihkan ke skema baru.')">
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
        <div class="modal-box" style="max-width: 580px; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.85rem;">
                <h3 class="modal-title" style="margin: 0;">Edit Master Data &amp; Biodata Asesi</h3>
                <button type="button" onclick="closeModal('editProfileModal')" style="background: none; border: none; font-size: 1.25rem; color: var(--text-muted); cursor: pointer; padding: 0.25rem; line-height: 1;">&times;</button>
            </div>
            <form action="{{ route('lsp.profile.update') }}" method="POST" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Nama Lengkap (Sesuai KTP) <span style="color: var(--accent-rose);">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ Auth::user()->name }}" required maxlength="50" placeholder="Nama lengkap...">
                    <small style="color: var(--text-muted); font-size: 0.725rem;">Maksimal 50 karakter, huruf dan tanda kutip saja.</small>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
                    <div class="form-group">
                        <label class="form-label" style="display: flex; justify-content: space-between;">
                            <span>NIK (KTP)</span>
                            <span style="font-size: 0.725rem; color: var(--text-muted);">Maks. 16 digit</span>
                        </label>
                        <input type="text" name="nik" id="edit_profile_nik" class="form-control" value="{{ Auth::user()->nik ?? ($myParticipant?->nik ?? '') }}" maxlength="16" placeholder="16 digit NIK..." inputmode="numeric">
                    </div>

                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin</label>
                        @php $userGender = Auth::user()->gender ?? ($myParticipant?->gender ?? ''); @endphp
                        <select name="gender" class="form-select">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki" {{ $userGender === 'Laki-laki' ? 'selected' : '' }}>Laki-laki</option>
                            <option value="Perempuan" {{ $userGender === 'Perempuan' ? 'selected' : '' }}>Perempuan</option>
                        </select>
                    </div>
                </div>

                @php
                    $userEdu = Auth::user()->education_type ?? ($myParticipant?->education_type ?? 'Siswa/Pelajar');
                    $userTitle = Auth::user()->academic_title ?? ($myParticipant?->academic_title ?? '');
                @endphp
                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
                    <div class="form-group">
                        <label class="form-label">Kategori Asesi <span style="color: var(--accent-rose);">*</span></label>
                        <select name="education_type" id="edit_profile_education_type" class="form-select" onchange="toggleProfileAcademicTitle()">
                            <option value="Siswa/Pelajar" {{ $userEdu === 'Siswa/Pelajar' ? 'selected' : '' }}>Siswa / Pelajar</option>
                            <option value="Mahasiswa" {{ $userEdu === 'Mahasiswa' ? 'selected' : '' }}>Mahasiswa</option>
                            <option value="Umum" {{ $userEdu === 'Umum' ? 'selected' : '' }}>Umum / Profesional</option>
                        </select>
                    </div>

                    <div class="form-group" id="editProfileAcademicTitleGroup" style="{{ in_array($userEdu, ['Mahasiswa', 'Umum']) ? '' : 'display: none;' }}">
                        <label class="form-label" id="editProfileAcademicTitleLabel">{{ $userEdu === 'Umum' ? 'Gelar Profesi / Akademik (Opsional)' : 'Gelar Mahasiswa (Opsional)' }}</label>
                        <input type="text" name="academic_title" id="edit_profile_academic_title" class="form-control" maxlength="50" placeholder="Contoh: S.Kom, S.T., A.Md., M.Kom" value="{{ $userTitle }}">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Email Akun (Terkunci)</label>
                    <input type="email" class="form-control" value="{{ Auth::user()->email }}" disabled style="background: #f1f5f9; color: #64748b;">
                </div>

                <div class="form-group" style="margin-bottom: 1rem;">
                    <label class="form-label" for="edit_profile_password">Password Baru (Opsional)</label>
                    <div style="position: relative;">
                        <input type="password" name="password" id="edit_profile_password" class="form-control" placeholder="Kosongkan jika tidak ingin mengubah password" minlength="6" style="padding-right: 2.75rem;">
                        <button type="button" onclick="togglePassword('edit_profile_password', this)" style="position: absolute; right: 0.75rem; top: 50%; transform: translateY(-50%); background: none; border: none; cursor: pointer; color: var(--text-muted); padding: 0.25rem; display: flex; align-items: center;" aria-label="Lihat password" title="Lihat password">
                            <svg class="eye-open" style="width: 18px; height: 18px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.036 12.322a1.012 1.012 0 010-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178z" />
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" />
                            </svg>
                            <svg class="eye-closed" style="width: 18px; height: 18px; display: none;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3.98 8.223A10.477 10.477 0 001.934 12C3.226 16.338 7.244 19.5 12 19.5c.993 0 1.953-.138 2.863-.395M6.228 6.228A10.45 10.45 0 0112 4.5c4.756 0 8.773 3.162 10.065 7.498a10.523 10.523 0 01-4.293 5.774M6.228 6.228L3 3m3.228 3.228l3.65 3.65m7.894 7.894L21 21m-3.228-3.228l-3.65-3.65m0 0a3 3 0 10-4.243-4.243m4.242 4.242L9.88 9.88" />
                            </svg>
                        </button>
                    </div>
                    <small style="color: var(--text-muted); font-size: 0.725rem;">Minimal 6 karakter. Isi hanya jika ingin memperbarui kata sandi login Anda.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor WhatsApp / HP Aktif <span style="color: var(--accent-rose);">*</span></label>
                    <input type="text" name="phone_number" class="form-control" value="{{ Auth::user()->phone_number }}" required maxlength="13" placeholder="08xxxxxxxxxx">
                    <small style="color: var(--text-muted); font-size: 0.725rem;">Maksimal 13 angka, digunakan untuk konfirmasi jadwal uji.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Domisili Lengkap</label>
                    <textarea name="address" class="form-control" rows="2" placeholder="Alamat domisili lengkap...">{{ Auth::user()->address ?: ($myParticipant?->address ?? '') }}</textarea>
                </div>

                <!-- Bagian Upload / Perbarui Dokumen Persyaratan Asesi -->
                <div style="margin-top: 1.25rem; border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
                    <div style="font-size: 0.825rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">
                        Dokumen Persyaratan Asesi (Maksimal 2MB per file)
                    </div>
                    <p style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.85rem;">
                        Pilih berkas baru jika Anda ingin memperbarui dokumen yang tersimpan. Format berkas: PDF/JPG/PNG/DOCX.
                    </p>

                    @php
                        $userKtp = Auth::user()->id_card_file ?: ($myParticipant?->id_card_file ?? null);
                        $userRapor = Auth::user()->report_sheet_file ?: ($myParticipant?->report_sheet_file ?? null);
                        $userCert = Auth::user()->certificate_file ?: ($myParticipant?->certificate_file ?? null);
                        $userDiploma = Auth::user()->diploma_file ?: ($myParticipant?->diploma_file ?? null);
                        $userPhoto = Auth::user()->photo_file ?: ($myParticipant?->photo_file ?? null);
                        $userPortfolio = Auth::user()->portfolio_file ?: ($myParticipant?->portfolio_file ?? null);
                    @endphp

                    <!-- 1. Foto KTP / Kartu Pelajar -->
                    <div class="form-group" style="margin-bottom: 0.85rem;">
                        <label class="form-label" style="display: flex; justify-content: space-between; align-items: center; font-size: 0.775rem;">
                            <span>1. Foto KTP / Kartu Pelajar</span>
                            @if($userKtp)
                                <a href="{{ asset($userKtp) }}" target="_blank" rel="noopener noreferrer" style="font-size: 0.725rem; color: var(--accent-primary); font-weight: 600; text-decoration: underline;">
                                    ✓ Berkas Aktif Tersimpan &rarr;
                                </a>
                            @else
                                <span style="font-size: 0.7rem; color: var(--text-dim); font-style: italic;">Belum diunggah</span>
                            @endif
                        </label>
                        <input type="file" name="id_card_file" class="form-control file-input-check" accept=".jpg,.jpeg,.png,.webp,.pdf" style="font-size: 0.775rem; padding: 0.35rem 0.5rem;">
                    </div>

                    <!-- 2. Rapor Semester 5 / Rapor Terakhir -->
                    <div class="form-group" style="margin-bottom: 0.85rem;">
                        <label class="form-label" style="display: flex; justify-content: space-between; align-items: center; font-size: 0.775rem;">
                            <span>2. Rapor Semester 5 / Rapor Terakhir</span>
                            @if($userRapor)
                                <a href="{{ asset($userRapor) }}" target="_blank" rel="noopener noreferrer" style="font-size: 0.725rem; color: var(--accent-primary); font-weight: 600; text-decoration: underline;">
                                    ✓ Berkas Aktif Tersimpan &rarr;
                                </a>
                            @else
                                <span style="font-size: 0.7rem; color: var(--text-dim); font-style: italic;">Belum diunggah</span>
                            @endif
                        </label>
                        <input type="file" name="report_sheet_file" class="form-control file-input-check" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp" style="font-size: 0.775rem; padding: 0.35rem 0.5rem;">
                    </div>

                    <!-- 3. Sertifikat Terkait -->
                    <div class="form-group" style="margin-bottom: 0.85rem;">
                        <label class="form-label" style="display: flex; justify-content: space-between; align-items: center; font-size: 0.775rem;">
                            <span>3. Sertifikat Terkait (Pelatihan / Prestasi)</span>
                            @if($userCert)
                                <a href="{{ asset($userCert) }}" target="_blank" rel="noopener noreferrer" style="font-size: 0.725rem; color: var(--accent-primary); font-weight: 600; text-decoration: underline;">
                                    ✓ Berkas Aktif Tersimpan &rarr;
                                </a>
                            @else
                                <span style="font-size: 0.7rem; color: var(--text-dim); font-style: italic;">Belum diunggah</span>
                            @endif
                        </label>
                        <input type="file" name="certificate_file" class="form-control file-input-check" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp" style="font-size: 0.775rem; padding: 0.35rem 0.5rem;">
                    </div>

                    <!-- 4. Ijazah Terakhir / Surat Keterangan Aktif -->
                    <div class="form-group" style="margin-bottom: 0.85rem;">
                        <label class="form-label" style="display: flex; justify-content: space-between; align-items: center; font-size: 0.775rem;">
                            <span>4. Ijazah Terakhir / Surat Keterangan Aktif</span>
                            @if($userDiploma)
                                <a href="{{ asset($userDiploma) }}" target="_blank" rel="noopener noreferrer" style="font-size: 0.725rem; color: var(--accent-primary); font-weight: 600; text-decoration: underline;">
                                    ✓ Berkas Aktif Tersimpan &rarr;
                                </a>
                            @else
                                <span style="font-size: 0.7rem; color: var(--text-dim); font-style: italic;">Belum diunggah</span>
                            @endif
                        </label>
                        <input type="file" name="diploma_file" class="form-control file-input-check" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp" style="font-size: 0.775rem; padding: 0.35rem 0.5rem;">
                    </div>

                    <!-- 5. Pas Foto Formal (3x4 / 4x6) -->
                    <div class="form-group" style="margin-bottom: 0.85rem;">
                        <label class="form-label" style="display: flex; justify-content: space-between; align-items: center; font-size: 0.775rem;">
                            <span>5. Pas Foto Formal (3x4 / 4x6)</span>
                            @if($userPhoto)
                                <a href="{{ asset($userPhoto) }}" target="_blank" rel="noopener noreferrer" style="font-size: 0.725rem; color: var(--accent-primary); font-weight: 600; text-decoration: underline;">
                                    ✓ Berkas Aktif Tersimpan &rarr;
                                </a>
                            @else
                                <span style="font-size: 0.7rem; color: var(--text-dim); font-style: italic;">Belum diunggah</span>
                            @endif
                        </label>
                        <input type="file" name="photo_file" class="form-control file-input-check" accept=".jpg,.jpeg,.png,.webp" style="font-size: 0.775rem; padding: 0.35rem 0.5rem;">
                    </div>

                    <!-- 6. Portofolio / Dokumen Pendukung Tambahan -->
                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label class="form-label" style="display: flex; justify-content: space-between; align-items: center; font-size: 0.775rem;">
                            <span>6. Portofolio / Dokumen Pendukung Tambahan</span>
                            @if($userPortfolio)
                                <a href="{{ asset($userPortfolio) }}" target="_blank" rel="noopener noreferrer" style="font-size: 0.725rem; color: var(--accent-primary); font-weight: 600; text-decoration: underline;">
                                    ✓ Berkas Aktif Tersimpan &rarr;
                                </a>
                            @else
                                <span style="font-size: 0.7rem; color: var(--text-dim); font-style: italic;">Belum diunggah</span>
                            @endif
                        </label>
                        <input type="file" name="portfolio_file" class="form-control file-input-check" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp,.zip" style="font-size: 0.775rem; padding: 0.35rem 0.5rem;">
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editProfileModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
                </div>
            </form>
        </div>
    </div>

    <!-- MODAL PENGUMPULAN TUGAS ASESI -->
    <div class="modal-overlay" id="submitTaskModal">
        <div class="modal-box" style="max-width: 580px; max-height: 90vh; overflow-y: auto;">
            <h3 class="modal-title" id="submitTaskTitle">Kumpulkan Bukti Tugas Ujian</h3>
            <form id="submitTaskForm" method="POST" action="" enctype="multipart/form-data">
                @csrf

                <div class="form-group">
                    <label class="form-label">Bukti Jawaban / Tautan Repository / Dokumen Portofolio</label>
                    <textarea name="submission_content" id="submissionContentInput" class="form-control" rows="5" placeholder="Tuliskan tautan GitHub, link Google Drive, atau uraian penyelesaian tugas praktikum Anda di sini..." required maxlength="2000"></textarea>
                    <small style="color: var(--text-muted); font-size: 0.725rem;">Pastikan tautan dapat diakses publik atau asesor uji kompetensi.</small>
                </div>

                <div class="form-group">
                    <label class="form-label">Unggah Gambar / Berkas Bukti (Maksimal 1MB / 1024 KB)</label>
                    <input type="file" name="submission_file" id="submissionFileInput" class="form-control" accept="image/png,image/jpeg,image/webp,image/jpg,.pdf,.doc,.docx,.zip">
                    <small style="color: var(--text-muted); font-size: 0.725rem; display: block; margin-top: 0.25rem;">
                        Format berkas: Gambar (PNG, JPG, JPEG, WEBP) atau Dokumen (PDF, DOC, DOCX, ZIP). Maksimal 1MB.
                    </small>
                    <div id="existingFileInfo" style="display: none; margin-top: 0.4rem; font-size: 0.75rem; color: #166534; background: #f0fdf4; border: 1px solid #bbf7d0; padding: 0.4rem 0.65rem; border-radius: var(--radius-xs);">
                        <span>Berkas saat ini: </span><a id="existingFileLink" href="#" target="_blank" style="font-weight: 600; text-decoration: underline; color: #166534;"></a>
                        <div style="font-size: 0.7rem; color: #15803d; margin-top: 0.15rem;">(Biarkan kosong jika tidak ingin mengubah berkas yang telah dikumpulkan).</div>
                    </div>
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
        <div class="modal-box" style="max-width: 560px; max-height: 90vh; overflow-y: auto;">
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

                <div class="form-group">
                    <label class="form-label">Batas Waktu Pengumpulan (Deadline Opsional)</label>
                    <input type="datetime-local" name="due_date" class="form-control">
                    <small style="color: var(--text-muted); font-size: 0.725rem;">Tentukan batas tanggal &amp; jam akhir penyerahan tugas ujian bagi asesi.</small>
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
        <div class="modal-box" style="max-width: 620px; max-height: 90vh; overflow-y: auto;">
            <h3 class="modal-title">Pemeriksaan Submisi Tugas Asesi</h3>
            <form id="reviewSubmissionForm" method="POST" action="">
                @csrf

                <div style="background: #f8fafc; border: 1px solid var(--border-subtle); padding: 0.85rem; border-radius: var(--radius-sm); margin-bottom: 1rem;">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap;">
                        <div>
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Asesi:</div>
                            <div id="reviewAsesiName" style="font-weight: 700; color: var(--text-primary);"></div>
                        </div>
                        <div style="text-align: right;">
                            <div style="font-size: 0.75rem; color: var(--text-muted);">Unit Tugas:</div>
                            <div id="reviewTaskTitle" style="font-size: 0.85rem; color: var(--accent-primary); font-weight: 600;"></div>
                        </div>
                    </div>

                    <!-- Bukti & Tautan Asesi -->
                    <div style="margin-top: 0.75rem; border-top: 1px solid var(--border-subtle); padding-top: 0.75rem;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Bukti Jawaban / Uraian:</div>
                        <div id="reviewContentDisplay" style="font-size: 0.825rem; color: var(--text-primary); line-height: 1.45; word-break: break-word; white-space: pre-wrap; max-height: 120px; overflow-y: auto;"></div>
                        <div id="reviewLinkContainer" style="margin-top: 0.4rem; display: none;"></div>
                    </div>

                    <!-- Pratinjau Berkas / Gambar -->
                    <div id="reviewFileContainer" style="margin-top: 0.75rem; border-top: 1px solid var(--border-subtle); padding-top: 0.75rem; display: none;">
                        <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Lampiran Berkas / Gambar:</div>
                        <div id="reviewFilePreview"></div>
                    </div>
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
        <div class="modal-box" style="max-width: 640px; max-height: 90vh; overflow-y: auto;">
            <h3 class="modal-title">Detail Bukti Pengerjaan Tugas</h3>
            <div style="margin-bottom: 1.25rem;">
                <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; margin-bottom: 0.75rem; flex-wrap: wrap;">
                    <div>
                        <div style="font-size: 0.75rem; color: var(--text-muted);">Nama Asesi:</div>
                        <div id="viewAsesiName" style="font-weight: 700; color: var(--text-primary);"></div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 0.75rem; color: var(--text-muted);">Unit Tugas:</div>
                        <div id="viewTaskTitle" style="font-weight: 600; color: var(--accent-primary);"></div>
                    </div>
                </div>

                <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.25rem;">Bukti / Jawaban Lengkap:</div>
                <div id="viewContentText" style="background: #f8fafc; border: 1px solid var(--border-subtle); padding: 0.85rem 1rem; border-radius: var(--radius-sm); font-size: 0.85rem; color: var(--text-primary); white-space: pre-wrap; word-break: break-word; max-height: 180px; overflow-y: auto;"></div>

                <div id="viewLinkContainer" style="margin-top: 0.5rem; display: none;"></div>

                <div id="viewFileSection" style="margin-top: 1rem; display: none;">
                    <div style="font-size: 0.75rem; color: var(--text-muted); margin-bottom: 0.35rem;">Lampiran Berkas / Gambar:</div>
                    <div id="viewFilePreview"></div>
                </div>
            </div>
            <div style="display: flex; justify-content: flex-end;">
                <button type="button" class="btn btn-secondary" onclick="closeModal('viewSubmissionModal')">Tutup</button>
            </div>
        </div>
    </div>

    <!-- MODAL LIHAT DETAIL MASTER BIODATA AKUN & SKEMA (ADMIN) -->
    <div class="modal-overlay" id="viewUserAccountModal">
        <div class="modal-box" style="max-width: 620px; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: flex-start; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 1rem;">
                <div style="display: flex; align-items: center; gap: 0.85rem;">
                    <div id="modalUserAvatar" style="width: 44px; height: 44px; border-radius: 50%; background: var(--accent-primary-light); color: var(--accent-primary); border: 1px solid var(--accent-primary-border); display: grid; place-items: center; font-size: 1.1rem; font-weight: 800; font-family: var(--font-mono); flex-shrink: 0;">
                        U
                    </div>
                    <div>
                        <div style="display: flex; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                            <h3 id="modalUserName" style="font-size: 1.15rem; font-weight: 800; color: var(--text-primary); margin: 0;"></h3>
                            <span id="modalUserRoleBadge" class="badge badge-code" style="font-size: 0.675rem;"></span>
                        </div>
                        <div id="modalUserEmail" style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.15rem;"></div>
                    </div>
                </div>
                <button type="button" onclick="closeModal('viewUserAccountModal')" style="background: none; border: none; font-size: 1.25rem; color: var(--text-muted); cursor: pointer; padding: 0.25rem; line-height: 1;">&times;</button>
            </div>

            <!-- Bagian 1: Biodata Lengkap Identitas -->
            <div style="margin-bottom: 1.25rem;">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.6rem;">
                    I. Biodata Lengkap Pengguna
                </div>
                <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem; font-size: 0.825rem;">
                    <div style="background: #f8fafc; border: 1px solid var(--border-subtle); padding: 0.65rem 0.85rem; border-radius: var(--radius-sm);">
                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 0.2rem;">NIK (Nomor Induk Kependudukan)</div>
                        <div id="modalUserNik" style="font-weight: 700; font-family: var(--font-mono); color: var(--text-primary);">-</div>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid var(--border-subtle); padding: 0.65rem 0.85rem; border-radius: var(--radius-sm);">
                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 0.2rem;">Jenis Kelamin</div>
                        <div id="modalUserGender" style="font-weight: 600; color: var(--text-primary);">-</div>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid var(--border-subtle); padding: 0.65rem 0.85rem; border-radius: var(--radius-sm);">
                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 0.2rem;">Kategori Asesi</div>
                        <div id="modalUserEduType" style="font-weight: 600; color: var(--text-primary);">-</div>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid var(--border-subtle); padding: 0.65rem 0.85rem; border-radius: var(--radius-sm);">
                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 0.2rem;">Gelar Akademik / Profesi</div>
                        <div id="modalUserAcademicTitle" style="font-weight: 600; color: var(--accent-primary);">-</div>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid var(--border-subtle); padding: 0.65rem 0.85rem; border-radius: var(--radius-sm);">
                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 0.2rem;">Nomor WhatsApp / HP</div>
                        <div id="modalUserPhone" style="font-weight: 600; font-family: var(--font-mono); color: var(--text-primary);">-</div>
                    </div>
                    <div style="background: #f8fafc; border: 1px solid var(--border-subtle); padding: 0.65rem 0.85rem; border-radius: var(--radius-sm);">
                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 0.2rem;">Tanggal Registrasi Akun</div>
                        <div id="modalUserCreated" style="font-weight: 600; color: var(--text-primary);">-</div>
                    </div>
                    <div style="grid-column: span 2; background: #f8fafc; border: 1px solid var(--border-subtle); padding: 0.65rem 0.85rem; border-radius: var(--radius-sm);">
                        <div style="font-size: 0.7rem; color: var(--text-muted); margin-bottom: 0.2rem;">Alamat Domisili Lengkap</div>
                        <div id="modalUserAddress" style="font-weight: 500; color: var(--text-primary); line-height: 1.4;">-</div>
                    </div>
                </div>
            </div>

            <!-- Bagian 2: Skema Sertifikasi yang Ditempuh -->
            <div style="margin-bottom: 1.25rem;">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.6rem;">
                    II. Skema Sertifikasi yang Ditempuh
                </div>
                <div id="modalSchemeContainer" style="background: #f8fafc; border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); padding: 0.85rem 1rem;">
                    <!-- Terisi dinamis via JS -->
                </div>
            </div>

            <!-- Bagian 3: Dokumen Persyaratan Asesi (KTP, Rapor, Sertifikat) -->
            <div style="margin-bottom: 1.5rem;">
                <div style="font-size: 0.75rem; font-weight: 700; color: var(--text-muted); text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.6rem;">
                    III. Dokumen Persyaratan Asesi
                </div>
                <div id="modalDocsContainer" style="display: grid; grid-template-columns: 1fr; gap: 0.5rem;">
                    <!-- Terisi dinamis via JS -->
                </div>
            </div>

            <div style="display: flex; justify-content: space-between; align-items: center; gap: 0.75rem; border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
                <form id="modalDeleteUserForm" method="POST" action="" style="margin: 0; display: none;" onsubmit="return confirm('Konfirmasi Hapus: Apakah Anda yakin ingin menghapus akun pengguna ini secara permanen?')">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger" style="font-size: 0.8rem; padding: 0.4rem 0.85rem;">
                        Hapus Akun Ini
                    </button>
                </form>
                <div style="margin-left: auto; display: flex; align-items: center; gap: 0.5rem;">
                    <button type="button" id="modalEditUserBtn" class="btn btn-primary" style="font-size: 0.8rem; padding: 0.4rem 0.85rem;" onclick="openEditUserFromDetail()">
                        Edit Biodata
                    </button>
                    <button type="button" class="btn btn-secondary" onclick="closeModal('viewUserAccountModal')" style="font-size: 0.8rem; padding: 0.4rem 0.85rem;">Tutup</button>
                </div>
            </div>
        </div>
    </div>

    <!-- MODAL EDIT BIODATA AKUN PENGGUNA (KHUSUS ADMIN) -->
    <div class="modal-overlay" id="editUserAccountModal">
        <div class="modal-box" style="max-width: 600px; max-height: 90vh; overflow-y: auto;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.25rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 0.85rem;">
                <h3 class="modal-title" style="margin: 0;">Edit Master Biodata Akun &amp; Berkas Asesi</h3>
                <button type="button" onclick="closeModal('editUserAccountModal')" style="background: none; border: none; font-size: 1.25rem; color: var(--text-muted); cursor: pointer; padding: 0.25rem; line-height: 1;">&times;</button>
            </div>
            <form id="editUserAccountForm" method="POST" action="" enctype="multipart/form-data">
                @csrf
                @method('PUT')

                <div class="form-group">
                    <label class="form-label">Nama Lengkap (Sesuai KTP) <span style="color: var(--accent-rose);">*</span></label>
                    <input type="text" name="name" id="admin_edit_user_name" class="form-control" required maxlength="50" placeholder="Nama lengkap...">
                    <small style="color: var(--text-muted); font-size: 0.725rem;">Maksimal 50 karakter, huruf dan tanda kutip saja.</small>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
                    <div class="form-group">
                        <label class="form-label">Email Akun Login <span style="color: var(--accent-rose);">*</span></label>
                        <input type="email" name="email" id="admin_edit_user_email" class="form-control" required maxlength="100" placeholder="nama@email.com">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Password Baru (Opsional)</label>
                        <input type="password" name="password" id="admin_edit_user_password" class="form-control" placeholder="Kosongkan jika tidak diubah" minlength="6">
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
                    <div class="form-group">
                        <label class="form-label">NIK (Nomor Induk Kependudukan)</label>
                        <input type="text" name="nik" id="admin_edit_user_nik" class="form-control" maxlength="16" placeholder="16 digit NIK..." inputmode="numeric">
                    </div>
                    <div class="form-group">
                        <label class="form-label">Jenis Kelamin</label>
                        <select name="gender" id="admin_edit_user_gender" class="form-select">
                            <option value="">-- Pilih Jenis Kelamin --</option>
                            <option value="Laki-laki">Laki-laki</option>
                            <option value="Perempuan">Perempuan</option>
                        </select>
                    </div>
                </div>

                <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 0.85rem;">
                    <div class="form-group">
                        <label class="form-label">Kategori Asesi</label>
                        <select name="education_type" id="admin_edit_user_edu" class="form-select" onchange="toggleAdminAcademicTitle()">
                            <option value="Siswa/Pelajar">Siswa / Pelajar</option>
                            <option value="Mahasiswa">Mahasiswa</option>
                            <option value="Umum">Umum / Profesional</option>
                        </select>
                    </div>
                    <div class="form-group" id="adminAcademicTitleGroup" style="display: none;">
                        <label class="form-label" id="adminAcademicTitleLabel">Gelar Mahasiswa (Opsional)</label>
                        <input type="text" name="academic_title" id="admin_edit_user_academic_title" class="form-control" maxlength="50" placeholder="Contoh: S.Kom, S.T., A.Md., M.Kom">
                    </div>
                </div>

                <div class="form-group">
                    <label class="form-label">Nomor WhatsApp / HP Aktif <span style="color: var(--accent-rose);">*</span></label>
                    <input type="text" name="phone_number" id="admin_edit_user_phone" class="form-control" required maxlength="13" placeholder="08xxxxxxxxxx">
                </div>

                <div class="form-group">
                    <label class="form-label">Alamat Domisili Lengkap</label>
                    <textarea name="address" id="admin_edit_user_address" class="form-control" rows="2" placeholder="Alamat domisili lengkap..."></textarea>
                </div>

                <!-- Bagian Berkas Persyaratan Asesi -->
                <div style="margin-top: 1.25rem; border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
                    <div style="font-size: 0.825rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">
                        Dokumen Persyaratan Asesi (Maksimal 2MB per file)
                    </div>
                    <p style="font-size: 0.725rem; color: var(--text-muted); margin-bottom: 0.75rem;">
                        Unggah berkas baru untuk memperbarui dokumen yang tersimpan.
                    </p>

                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label class="form-label" style="display: flex; justify-content: space-between; align-items: center; font-size: 0.775rem;">
                            <span>1. Foto KTP / Kartu Pelajar</span>
                            <span id="adminCurrentIdCardLink"></span>
                        </label>
                        <input type="file" name="id_card_file" class="form-control file-input-check" accept=".jpg,.jpeg,.png,.webp,.pdf" style="font-size: 0.775rem; padding: 0.35rem 0.5rem;">
                    </div>

                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label class="form-label" style="display: flex; justify-content: space-between; align-items: center; font-size: 0.775rem;">
                            <span>2. Rapor Semester 5 / Rapor Terakhir</span>
                            <span id="adminCurrentReportLink"></span>
                        </label>
                        <input type="file" name="report_sheet_file" class="form-control file-input-check" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp" style="font-size: 0.775rem; padding: 0.35rem 0.5rem;">
                    </div>

                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label class="form-label" style="display: flex; justify-content: space-between; align-items: center; font-size: 0.775rem;">
                            <span>3. Sertifikat Terkait</span>
                            <span id="adminCurrentCertLink"></span>
                        </label>
                        <input type="file" name="certificate_file" class="form-control file-input-check" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp" style="font-size: 0.775rem; padding: 0.35rem 0.5rem;">
                    </div>

                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label class="form-label" style="display: flex; justify-content: space-between; align-items: center; font-size: 0.775rem;">
                            <span>4. Ijazah Terakhir / Surat Keterangan Aktif</span>
                            <span id="adminCurrentDiplomaLink"></span>
                        </label>
                        <input type="file" name="diploma_file" class="form-control file-input-check" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp" style="font-size: 0.775rem; padding: 0.35rem 0.5rem;">
                    </div>

                    <div class="form-group" style="margin-bottom: 0.75rem;">
                        <label class="form-label" style="display: flex; justify-content: space-between; align-items: center; font-size: 0.775rem;">
                            <span>5. Pas Foto Formal (3x4 / 4x6)</span>
                            <span id="adminCurrentPhotoLink"></span>
                        </label>
                        <input type="file" name="photo_file" class="form-control file-input-check" accept=".jpg,.jpeg,.png,.webp" style="font-size: 0.775rem; padding: 0.35rem 0.5rem;">
                    </div>

                    <div class="form-group" style="margin-bottom: 0.5rem;">
                        <label class="form-label" style="display: flex; justify-content: space-between; align-items: center; font-size: 0.775rem;">
                            <span>6. Portofolio / Dokumen Pendukung Tambahan</span>
                            <span id="adminCurrentPortfolioLink"></span>
                        </label>
                        <input type="file" name="portfolio_file" class="form-control file-input-check" accept=".pdf,.doc,.docx,.jpg,.jpeg,.png,.webp,.zip" style="font-size: 0.775rem; padding: 0.35rem 0.5rem;">
                    </div>
                </div>

                <div style="display: flex; justify-content: flex-end; gap: 0.75rem; margin-top: 1.5rem; border-top: 1px solid var(--border-subtle); padding-top: 1rem;">
                    <button type="button" class="btn btn-secondary" onclick="closeModal('editUserAccountModal')">Batal</button>
                    <button type="submit" class="btn btn-primary">Simpan Pembaruan</button>
                </div>
            </form>
        </div>
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

    function openSubmitTaskModal(taskId, title, content, fileName, fileUrl) {
        const el = document.getElementById('submitTaskModal');
        const form = document.getElementById('submitTaskForm');
        const titleEl = document.getElementById('submitTaskTitle');
        const contentEl = document.getElementById('submissionContentInput');
        const fileInput = document.getElementById('submissionFileInput');
        const existingInfo = document.getElementById('existingFileInfo');
        const existingLink = document.getElementById('existingFileLink');

        if (form) form.action = `/lsp/tasks/${taskId}/submit`;
        if (titleEl) titleEl.innerText = `Kumpulkan Bukti: ${title}`;
        if (contentEl) contentEl.value = content || '';
        if (fileInput) fileInput.value = '';

        if (existingInfo && existingLink) {
            if (fileName && fileName.trim() !== '') {
                existingInfo.style.display = 'block';
                existingLink.innerText = fileName;
                existingLink.href = fileUrl || '#';
            } else {
                existingInfo.style.display = 'none';
            }
        }

        if (el) el.classList.add('active');
    }

    // Validasi ukuran file di sisi client (maksimal 1MB)
    const subFileInput = document.getElementById('submissionFileInput');
    if (subFileInput) {
        subFileInput.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const maxBytes = 1024 * 1024; // 1MB
                if (this.files[0].size > maxBytes) {
                    alert('Ukuran berkas melebihi batas maksimal 1MB (1024 KB). File Anda berukuran: ' + (this.files[0].size / 1024 / 1024).toFixed(2) + ' MB. Silakan pilih berkas yang lebih kecil.');
                    this.value = '';
                }
            }
        });
    }

    function extractUrls(text) {
        if (!text) return [];
        const matches = text.match(/https?:\/\/[^\s]+/g);
        return matches ? Array.from(new Set(matches)) : [];
    }

    function renderFilePreview(container, sub) {
        if (!container) return;
        container.innerHTML = '';
        if (!sub || !sub.file_path) {
            return;
        }

        const fileUrl = sub.file_url || ('/' + sub.file_path.replace(/^\/+/, ''));
        const fileName = sub.file_name || 'Lampiran Tugas';
        const isImage = sub.is_image || /\.(png|jpe?g|webp|gif|svg)$/i.test(sub.file_path);

        if (isImage) {
            container.innerHTML = `
                <div style="background: #ffffff; border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); padding: 0.65rem; margin-top: 0.25rem;">
                    <a href="${fileUrl}" target="_blank" rel="noopener noreferrer" style="display: block; text-decoration: none;">
                        <img src="${fileUrl}" alt="${fileName}" style="max-height: 220px; max-width: 100%; object-fit: contain; border-radius: 4px; display: block; margin: 0 auto 0.5rem; border: 1px solid #e2e8f0; cursor: zoom-in;" title="Klik untuk membuka gambar ukuran penuh">
                    </a>
                    <div style="display: flex; justify-content: space-between; align-items: center; font-size: 0.75rem; flex-wrap: wrap; gap: 0.5rem;">
                        <span style="font-weight: 600; color: var(--text-primary);">${fileName}</span>
                        <a href="${fileUrl}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="font-size: 0.725rem; padding: 0.2rem 0.6rem; color: #166534; background: #f0fdf4; border-color: #bbf7d0;">
                            🖼️ Buka Gambar Penuh &rarr;
                        </a>
                    </div>
                </div>
            `;
        } else {
            container.innerHTML = `
                <div style="background: #ffffff; border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); padding: 0.65rem; display: flex; justify-content: space-between; align-items: center; margin-top: 0.25rem; flex-wrap: wrap; gap: 0.5rem;">
                    <div style="display: flex; align-items: center; gap: 0.4rem; font-size: 0.8rem; font-weight: 600; color: var(--text-primary);">
                        <svg style="width: 18px; height: 18px; color: var(--accent-primary);" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z" />
                        </svg>
                        <span>${fileName}</span>
                    </div>
                    <a href="${fileUrl}" target="_blank" download class="btn btn-secondary" style="font-size: 0.725rem; padding: 0.25rem 0.65rem; color: #1e40af; background: #eff6ff; border-color: #bfdbfe;">
                        📎 Unduh Berkas
                    </a>
                </div>
            `;
        }
    }

    function renderLinks(container, urls) {
        if (!container) return;
        container.innerHTML = '';
        if (!urls || urls.length === 0) {
            container.style.display = 'none';
            return;
        }

        container.style.display = 'flex';
        container.style.flexWrap = 'wrap';
        container.style.gap = '0.4rem';

        urls.forEach((url, idx) => {
            const a = document.createElement('a');
            a.href = url;
            a.target = '_blank';
            a.rel = 'noopener noreferrer';
            a.className = 'btn btn-secondary';
            a.style.fontSize = '0.75rem';
            a.style.padding = '0.25rem 0.6rem';
            a.style.color = 'var(--accent-primary)';
            a.style.fontWeight = '600';
            a.innerHTML = `🔗 Buka Tautan: <span style="text-decoration: underline; margin-left: 0.2rem;">${url.length > 35 ? url.substring(0, 32) + '...' : url}</span>`;
            container.appendChild(a);
        });
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
        document.getElementById('reviewContentDisplay').innerText = sub.submission_content || '-';
        document.getElementById('reviewStatusSelect').value = sub.status === 'Ditolak' ? 'Ditolak' : 'Diterima';
        document.getElementById('reviewNotesInput').value = sub.admin_notes || '';

        const urls = extractUrls(sub.submission_content);
        renderLinks(document.getElementById('reviewLinkContainer'), urls);

        const fileContainer = document.getElementById('reviewFileContainer');
        const filePreview = document.getElementById('reviewFilePreview');
        if (sub.file_path) {
            if (fileContainer) fileContainer.style.display = 'block';
            renderFilePreview(filePreview, sub);
        } else {
            if (fileContainer) fileContainer.style.display = 'none';
        }

        if (el) el.classList.add('active');
    }

    function openViewSubmissionModal(sub) {
        const el = document.getElementById('viewSubmissionModal');
        const asesiName = sub.participant ? sub.participant.full_name : (sub.user ? sub.user.name : 'Asesi');
        const taskTitle = sub.task ? `${sub.task.task_code} - ${sub.task.title}` : 'Unit Tugas';

        document.getElementById('viewAsesiName').innerText = asesiName;
        document.getElementById('viewTaskTitle').innerText = taskTitle;
        document.getElementById('viewContentText').innerText = sub.submission_content || '-';

        const urls = extractUrls(sub.submission_content);
        renderLinks(document.getElementById('viewLinkContainer'), urls);

        const fileSection = document.getElementById('viewFileSection');
        const filePreview = document.getElementById('viewFilePreview');
        if (sub.file_path) {
            if (fileSection) fileSection.style.display = 'block';
            renderFilePreview(filePreview, sub);
        } else {
            if (fileSection) fileSection.style.display = 'none';
        }

        if (el) el.classList.add('active');
    }

    // Real-time sanitization: NIK hanya angka & maks 16 digit
    const editNikInput = document.getElementById('edit_profile_nik');
    if (editNikInput) {
        editNikInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);
        });
    }

    // Overlay click listener
    document.querySelectorAll('.modal-overlay').forEach(modal => {
        modal.addEventListener('click', (e) => {
            if (e.target === modal) modal.classList.remove('active');
        });
    });

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

    // Modal Master Akun & Biodata Lengkap Asesi
    function openUserAccountModal(user, part, sch) {
        const el = document.getElementById('viewUserAccountModal');
        if (!el) return;

        const userName = user ? user.name : (part ? part.full_name : 'Pengguna');
        const userEmail = user ? user.email : (part ? part.email : '-');
        const userRole = (user && user.role) ? user.role.toUpperCase() : 'ASESI';
        const userNik = (part && part.nik) ? part.nik : ((user && user.nik) ? user.nik : '-');
        const userGender = (part && part.gender) ? part.gender : ((user && user.gender) ? user.gender : '-');
        const userPhone = (part && part.phone_number) ? part.phone_number : '-';
        const userAddress = (part && part.address) ? part.address : '-';

        let userCreated = '-';
        if (user && user.created_at) {
            try {
                const d = new Date(user.created_at);
                userCreated = d.toLocaleDateString('id-ID', { day: '2-digit', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit' });
            } catch (e) {
                userCreated = user.created_at;
            }
        }

        const avatarEl = document.getElementById('modalUserAvatar');
        if (avatarEl) avatarEl.innerText = userName.charAt(0).toUpperCase();

        const nameEl = document.getElementById('modalUserName');
        if (nameEl) nameEl.innerText = userName;

        const emailEl = document.getElementById('modalUserEmail');
        if (emailEl) emailEl.innerText = userEmail;

        const roleBadge = document.getElementById('modalUserRoleBadge');
        if (roleBadge) {
            roleBadge.innerText = userRole;
            roleBadge.style.color = userRole === 'ADMIN' ? 'var(--accent-purple)' : 'var(--accent-primary)';
        }

        const nikEl = document.getElementById('modalUserNik');
        if (nikEl) nikEl.innerText = userNik;

        const genderEl = document.getElementById('modalUserGender');
        if (genderEl) genderEl.innerText = userGender;

        const phoneEl = document.getElementById('modalUserPhone');
        if (phoneEl) phoneEl.innerText = userPhone;

        const createdEl = document.getElementById('modalUserCreated');
        if (createdEl) createdEl.innerText = userCreated;

        const addressEl = document.getElementById('modalUserAddress');
        if (addressEl) addressEl.innerText = userAddress;

        const schemeContainer = document.getElementById('modalSchemeContainer');
        if (schemeContainer) {
            if (sch && part) {
                let statusBadgeClass = 'badge-pending';
                if (part.status && part.status.includes('Kompeten') && !part.status.includes('Belum')) {
                    statusBadgeClass = 'badge-kompeten';
                } else if (part.status && part.status.includes('Belum')) {
                    statusBadgeClass = 'badge-belum';
                }

                schemeContainer.innerHTML = `
                    <div style="display: flex; justify-content: space-between; align-items: flex-start; gap: 1rem; flex-wrap: wrap; margin-bottom: 0.5rem;">
                        <div>
                            <span class="badge badge-code" style="color: var(--accent-primary); font-size: 0.725rem;">${sch.scheme_code || '-'}</span>
                            <div style="font-size: 0.95rem; font-weight: 700; color: var(--text-primary); margin-top: 0.25rem;">
                                ${sch.scheme_name || 'Skema Sertifikasi'}
                            </div>
                        </div>
                        <span class="badge ${statusBadgeClass}" style="font-size: 0.775rem;">
                            <span style="width: 6px; height: 6px; border-radius: 50%; background: currentColor;"></span>
                            ${part.status || 'Pending'}
                        </span>
                    </div>
                    <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.5rem; font-size: 0.775rem; margin-top: 0.65rem; border-top: 1px dashed var(--border-subtle); padding-top: 0.65rem;">
                        <div>
                            <span style="color: var(--text-muted);">No. Registrasi:</span>
                            <span style="font-family: var(--font-mono); font-weight: 700; color: var(--text-primary); margin-left: 0.25rem;">${part.registration_number || '-'}</span>
                        </div>
                        <div>
                            <span style="color: var(--text-muted);">Terdaftar:</span>
                            <span style="color: var(--text-primary); margin-left: 0.25rem;">${part.created_at ? new Date(part.created_at).toLocaleDateString('id-ID', {day: '2-digit', month: 'short', year: 'numeric'}) : '-'}</span>
                        </div>
                    </div>
                    ${sch.description ? `<div style="font-size: 0.75rem; color: var(--text-muted); margin-top: 0.5rem; line-height: 1.4;">${sch.description}</div>` : ''}
                `;
            } else {
                schemeContainer.innerHTML = `
                    <div style="text-align: center; padding: 1rem; color: var(--text-muted); font-size: 0.8rem;">
                        <span style="display: inline-block; margin-bottom: 0.25rem; font-size: 1.25rem;">ℹ️</span>
                        <div>Pengguna ini belum terdaftar pada skema sertifikasi apapun.</div>
                    </div>
                `;
            }
        }

        const userEduType = (part && part.education_type) ? part.education_type : ((user && user.education_type) ? user.education_type : 'Siswa/Pelajar');
        const userAcademicTitle = (part && part.academic_title) ? part.academic_title : ((user && user.academic_title) ? user.academic_title : '-');

        const eduTypeEl = document.getElementById('modalUserEduType');
        if (eduTypeEl) eduTypeEl.innerText = userEduType;

        const academicTitleEl = document.getElementById('modalUserAcademicTitle');
        if (academicTitleEl) academicTitleEl.innerText = ((userEduType === 'Mahasiswa' || userEduType === 'Umum') && userAcademicTitle && userAcademicTitle !== '-') ? userAcademicTitle : '-';

        // Dokumen Persyaratan Asesi
        const docsContainer = document.getElementById('modalDocsContainer');
        if (docsContainer) {
            const idCard = (part && part.id_card_file) ? part.id_card_file : ((user && user.id_card_file) ? user.id_card_file : null);
            const reportSheet = (part && part.report_sheet_file) ? part.report_sheet_file : ((user && user.report_sheet_file) ? user.report_sheet_file : null);
            const certificate = (part && part.certificate_file) ? part.certificate_file : ((user && user.certificate_file) ? user.certificate_file : null);
            const diploma = (part && part.diploma_file) ? part.diploma_file : ((user && user.diploma_file) ? user.diploma_file : null);
            const photo = (part && part.photo_file) ? part.photo_file : ((user && user.photo_file) ? user.photo_file : null);
            const portfolio = (part && part.portfolio_file) ? part.portfolio_file : ((user && user.portfolio_file) ? user.portfolio_file : null);

            function renderDocItem(title, filePath) {
                if (filePath) {
                    const cleanPath = '/' + filePath.replace(/^\/+/, '');
                    const isImg = /\.(png|jpe?g|webp|gif)$/i.test(filePath);
                    return `
                        <div style="background: #ffffff; border: 1px solid var(--border-subtle); padding: 0.6rem 0.85rem; border-radius: var(--radius-sm); display: flex; justify-content: space-between; align-items: center; gap: 0.5rem; flex-wrap: wrap;">
                            <div>
                                <div style="font-weight: 600; font-size: 0.8rem; color: var(--text-primary);">${title}</div>
                                <div style="font-size: 0.7rem; color: #15803d; font-family: var(--font-mono);">Berkas tersedia (${filePath.split('/').pop()})</div>
                            </div>
                            <div style="display: flex; gap: 0.35rem;">
                                <a href="${cleanPath}" target="_blank" rel="noopener noreferrer" class="btn btn-secondary" style="font-size: 0.725rem; padding: 0.25rem 0.65rem; color: var(--accent-primary);">
                                    ${isImg ? '🖼️ Lihat Foto' : '📄 Buka Berkas'} &rarr;
                                </a>
                            </div>
                        </div>
                    `;
                } else {
                    return `
                        <div style="background: #f8fafc; border: 1px dashed var(--border-subtle); padding: 0.55rem 0.85rem; border-radius: var(--radius-sm); display: flex; justify-content: space-between; align-items: center;">
                            <span style="font-size: 0.8rem; color: var(--text-muted);">${title}</span>
                            <span style="font-size: 0.725rem; color: var(--text-dim); font-style: italic;">Belum diunggah</span>
                        </div>
                    `;
                }
            }

            docsContainer.innerHTML = `
                ${renderDocItem('1. Foto KTP / Kartu Pelajar', idCard)}
                ${renderDocItem('2. Rapor Semester 5 / Rapor Terakhir', reportSheet)}
                ${renderDocItem('3. Sertifikat Terkait', certificate)}
                ${renderDocItem('4. Ijazah Terakhir / Surat Keterangan Aktif', diploma)}
                ${renderDocItem('5. Pas Foto Formal (3x4 / 4x6)', photo)}
                ${renderDocItem('6. Portofolio / Berkas Pendukung Tambahan', portfolio)}
            `;
        }

        const delForm = document.getElementById('modalDeleteUserForm');
        if (delForm) {
            const currentAuthId = {{ Auth::id() }};
            if (user && user.id && user.id !== currentAuthId && user.role !== 'admin') {
                delForm.action = `/lsp/users/${user.id}`;
                delForm.style.display = 'inline-block';
            } else {
                delForm.style.display = 'none';
            }
        }

        currentUserAccount = user;
        currentPartAccount = part;

        el.classList.add('active');
    }

    let currentUserAccount = null;
    let currentPartAccount = null;

    function openEditUserFromDetail() {
        if (currentUserAccount) {
            openEditUserModal(currentUserAccount, currentPartAccount);
        }
    }

    function toggleAdminAcademicTitle() {
        const edu = document.getElementById('admin_edit_user_edu');
        const group = document.getElementById('adminAcademicTitleGroup');
        const input = document.getElementById('admin_edit_user_academic_title');
        const label = document.getElementById('adminAcademicTitleLabel');
        if (edu && group) {
            if (edu.value === 'Mahasiswa' || edu.value === 'Umum') {
                group.style.display = 'block';
                if (label) {
                    label.innerText = (edu.value === 'Umum' ? 'Gelar Profesi / Akademik' : 'Gelar Mahasiswa') + ' (Opsional)';
                }
            } else {
                group.style.display = 'none';
                if (input) input.value = '';
            }
        }
    }

    function toggleProfileAcademicTitle() {
        const edu = document.getElementById('edit_profile_education_type');
        const group = document.getElementById('editProfileAcademicTitleGroup');
        const input = document.getElementById('edit_profile_academic_title');
        const label = document.getElementById('editProfileAcademicTitleLabel');
        if (edu && group) {
            if (edu.value === 'Mahasiswa' || edu.value === 'Umum') {
                group.style.display = 'block';
                if (label) {
                    label.innerText = (edu.value === 'Umum' ? 'Gelar Profesi / Akademik' : 'Gelar Mahasiswa') + ' (Opsional)';
                }
            } else {
                group.style.display = 'none';
                if (input) input.value = '';
            }
        }
    }

    function openEditUserModal(user, part) {
        if (!user) return;
        currentUserAccount = user;
        currentPartAccount = part;

        const el = document.getElementById('editUserAccountModal');
        const form = document.getElementById('editUserAccountForm');
        if (!el || !form) return;

        form.action = `/lsp/users/${user.id}`;

        const nameVal = user.name || (part ? part.full_name : '');
        const emailVal = user.email || (part ? part.email : '');
        const nikVal = (part && part.nik) ? part.nik : (user.nik || '');
        const genderVal = (part && part.gender) ? part.gender : (user.gender || '');
        const eduVal = (part && part.education_type) ? part.education_type : (user.education_type || 'Siswa/Pelajar');
        const titleVal = (part && part.academic_title) ? part.academic_title : (user.academic_title || '');
        const phoneVal = (part && part.phone_number) ? part.phone_number : (user.phone_number || '');
        const addressVal = (part && part.address) ? part.address : (user.address || '');

        const nameInput = document.getElementById('admin_edit_user_name');
        if (nameInput) nameInput.value = nameVal;

        const emailInput = document.getElementById('admin_edit_user_email');
        if (emailInput) emailInput.value = emailVal;

        const passInput = document.getElementById('admin_edit_user_password');
        if (passInput) passInput.value = '';

        const nikInput = document.getElementById('admin_edit_user_nik');
        if (nikInput) nikInput.value = nikVal;

        const genderInput = document.getElementById('admin_edit_user_gender');
        if (genderInput) genderInput.value = genderVal;

        const eduInput = document.getElementById('admin_edit_user_edu');
        if (eduInput) eduInput.value = eduVal;

        const titleInput = document.getElementById('admin_edit_user_academic_title');
        if (titleInput) titleInput.value = titleVal;

        const phoneInput = document.getElementById('admin_edit_user_phone');
        if (phoneInput) phoneInput.value = phoneVal;

        const addressInput = document.getElementById('admin_edit_user_address');
        if (addressInput) addressInput.value = addressVal;

        toggleAdminAcademicTitle();

        // Tampilkan info berkas aktif (6 berkas)
        const idCard = (part && part.id_card_file) ? part.id_card_file : (user.id_card_file || null);
        const reportSheet = (part && part.report_sheet_file) ? part.report_sheet_file : (user.report_sheet_file || null);
        const cert = (part && part.certificate_file) ? part.certificate_file : (user.certificate_file || null);
        const diploma = (part && part.diploma_file) ? part.diploma_file : (user.diploma_file || null);
        const photo = (part && part.photo_file) ? part.photo_file : (user.photo_file || null);
        const portfolio = (part && part.portfolio_file) ? part.portfolio_file : (user.portfolio_file || null);

        function setupAdminLink(elId, path) {
            const el = document.getElementById(elId);
            if (el) {
                el.innerHTML = path ? `<a href="/${path.replace(/^\/+/, '')}" target="_blank" rel="noopener noreferrer" style="color: var(--accent-primary); font-weight: 600; text-decoration: underline;">✓ Berkas Aktif &rarr;</a>` : '<span style="color: var(--text-dim); font-style: italic;">Belum diunggah</span>';
            }
        }

        setupAdminLink('adminCurrentIdCardLink', idCard);
        setupAdminLink('adminCurrentReportLink', reportSheet);
        setupAdminLink('adminCurrentCertLink', cert);
        setupAdminLink('adminCurrentDiplomaLink', diploma);
        setupAdminLink('adminCurrentPhotoLink', photo);
        setupAdminLink('adminCurrentPortfolioLink', portfolio);

        closeModal('viewUserAccountModal');
        el.classList.add('active');
    }

    // Validasi ukuran berkas (maksimal 2MB / 2048 KB)
    document.querySelectorAll('.file-input-check').forEach(function(input) {
        input.addEventListener('change', function() {
            if (this.files && this.files[0]) {
                const maxBytes = 2 * 1024 * 1024; // 2MB
                if (this.files[0].size > maxBytes) {
                    alert('Ukuran berkas melebihi batas maksimal 2MB (2048 KB). File Anda berukuran: ' + (this.files[0].size / 1024 / 1024).toFixed(2) + ' MB. Silakan pilih berkas yang lebih kecil.');
                    this.value = '';
                }
            }
        });
    });

    // Sanitasi NIK admin form
    const adminNikInput = document.getElementById('admin_edit_user_nik');
    if (adminNikInput) {
        adminNikInput.addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '').slice(0, 16);
        });
    }

    // Filter Pencarian Real-Time Master Akun
    function filterAccountsTable() {
        const input = document.getElementById('accountSearchInput');
        if (!input) return;
        const filter = input.value.toLowerCase().trim();
        const table = document.getElementById('accountsTable');
        if (!table) return;
        const rows = table.querySelectorAll('tbody tr.account-row');

        rows.forEach(row => {
            const text = (row.getAttribute('data-search') || row.innerText).toLowerCase();
            row.style.display = text.includes(filter) ? '' : 'none';
        });
    }
</script>
@endpush
@endsection
