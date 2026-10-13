@extends('layouts.astro')

@section('title', 'Hasil Asesmen Kompetensi - LSP System')

@section('content')
<!-- Header Modul Hasil Asesmen -->
<div class="page-header">
    <div>
        <h2 class="page-title">Hasil Asesmen &amp; Rekomendasi Kelulusan</h2>
        <p class="page-desc">Informasi resmi pencapaian kompetensi dan status verifikasi tugas ujian BNSP.</p>
    </div>
    <div>
        <a href="{{ route('lsp.dashboard') }}" class="btn btn-secondary">
            &larr; Kembali ke Dashboard
        </a>
    </div>
</div>

@if($participant)
    <!-- Kartu Hasil Keputusan Asesmen Utama -->
    <div style="background: #ffffff; border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 2rem; box-shadow: var(--shadow-card); margin-bottom: 2rem;">
        <div style="display: flex; justify-content: space-between; align-items: flex-start; flex-wrap: wrap; gap: 1.5rem; border-bottom: 1px solid var(--border-subtle); padding-bottom: 1.5rem; margin-bottom: 1.5rem;">
            <div>
                <span class="badge badge-code" style="color: #4338ca; font-size: 0.85rem; padding: 0.25rem 0.65rem;">
                    {{ $participant->registration_number }}
                </span>
                <h3 style="font-size: 1.5rem; font-weight: 800; color: var(--text-primary); margin-top: 0.5rem;">
                    {{ $participant->scheme->scheme_name ?? 'Skema Belum Terpilih' }}
                </h3>
                <p style="font-size: 0.875rem; color: var(--text-muted); margin-top: 0.25rem;">
                    Kode Unit Skema: <span style="font-family: var(--font-mono); font-weight: 600; color: var(--text-secondary);">{{ $participant->scheme->scheme_code ?? '-' }}</span>
                </p>
            </div>

            <div style="display: flex; flex-direction: column; align-items: flex-end; gap: 0.5rem;">
                <div style="font-size: 0.75rem; color: var(--text-muted); text-transform: uppercase; font-weight: 700; letter-spacing: 0.05em;">
                    Keputusan Asesor Penguji
                </div>
                <span class="badge {{ str_contains($participant->status, 'Kompeten') && !str_contains($participant->status, 'Belum') ? 'badge-kompeten' : 'badge-belum' }}" style="font-size: 1rem; padding: 0.5rem 1.25rem; font-weight: 700;">
                    <span style="width: 8px; height: 8px; border-radius: 50%; background: currentColor;"></span>
                    {{ $participant->status }}
                </span>
            </div>
        </div>

        <!-- Indikator Metrik Penyelesaian Tugas Ujian (Syarat Mutlak Kelulusan) -->
        <div style="background: #f8fafc; border: 1px solid var(--border-subtle); border-radius: var(--radius-sm); padding: 1.25rem; margin-bottom: 1.5rem;">
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 0.5rem; flex-wrap: wrap; gap: 0.5rem;">
                <div>
                    <span style="font-weight: 700; color: var(--text-primary); font-size: 0.925rem;">
                        Indikator Metrik Penyelesaian Tugas Ujian Praktik
                    </span>
                    <span style="font-size: 0.8rem; color: var(--text-muted); display: block; margin-top: 0.15rem;">
                        Syarat Mutlak Kompetensi: 100% unit tugas ujian wajib diselesaikan dan berstatus Diterima oleh asesor penguji.
                    </span>
                </div>
                <div>
                    <span class="badge {{ $participant->isEligibleForCompetence() ? 'badge-kompeten' : 'badge-belum' }}" style="font-size: 0.8rem; padding: 0.35rem 0.75rem;">
                        {{ $participant->acceptedTasksCount() }} / {{ $participant->totalTasksCount() }} Unit Diterima ({{ $participant->taskProgressPercentage() }}%)
                    </span>
                </div>
            </div>
            <div style="background: #e2e8f0; border-radius: 999px; height: 8px; width: 100%; overflow: hidden; margin-bottom: 0.5rem;">
                <div style="height: 100%; width: {{ $participant->taskProgressPercentage() }}%; background: {{ $participant->isEligibleForCompetence() ? 'var(--accent-emerald)' : '#f59e0b' }}; transition: width 0.3s ease;"></div>
            </div>
            <div style="display: flex; justify-content: space-between; font-size: 0.775rem; color: var(--text-muted); flex-wrap: wrap; gap: 0.5rem;">
                <span>
                    @if($participant->totalTasksCount() === 0)
                        <span style="color: #64748b;">Belum ada unit soal ujian yang ditugaskan pada skema ini.</span>
                    @elseif($participant->isEligibleForCompetence())
                        <b style="color: var(--accent-emerald);">✓ Syarat Kompetensi Terpenuhi:</b> Seluruh unit tugas ujian telah berstatus Diterima asesor penguji.
                    @else
                        <b style="color: #b45309;">⚠ Belum Memenuhi Syarat:</b> Sisa {{ $participant->totalTasksCount() - $participant->acceptedTasksCount() }} unit tugas ujian belum berstatus Diterima.
                    @endif
                </span>
                <span style="font-family: var(--font-mono); font-weight: 700; color: var(--text-primary);">
                    Progress Tugas: {{ $participant->taskProgressPercentage() }}%
                </span>
            </div>
        </div>

        <!-- Penjelasan Status & Rekomendasi -->
        @if(str_contains($participant->status, 'Kompeten') && !str_contains($participant->status, 'Belum'))
            <div style="background: var(--accent-emerald-bg); border: 1px solid var(--accent-emerald-border); border-radius: var(--radius-sm); padding: 1.25rem; display: flex; gap: 1rem; align-items: flex-start; margin-bottom: 1.5rem;">
                <div style="color: var(--accent-emerald); font-size: 1.25rem; flex-shrink: 0; line-height: 1;">
                    <svg style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12.75L11.25 15 15 9.75M21 12a9 9 0 11-18 0 9 9 0 0118 0z" />
                    </svg>
                </div>
                <div>
                    <div style="font-weight: 700; color: var(--accent-emerald); font-size: 0.95rem; margin-bottom: 0.25rem;">
                        Rekomendasi: Peserta Dinyatakan Kompeten (K)
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5;">
                        Selamat! Seluruh kriteria unjuk kerja, portofolio, dan penyelesaian tugas ujian praktik Anda telah memenuhi standar kompetensi kerja nasional (SKKNI). Sertifikat kompetensi resmi BNSP dapat diproses sesuai prosedur LSP.
                    </div>
                </div>
            </div>
        @else
            <div style="background: var(--accent-amber-bg); border: 1px solid var(--accent-amber-border); border-radius: var(--radius-sm); padding: 1.25rem; display: flex; gap: 1rem; align-items: flex-start; margin-bottom: 1.5rem;">
                <div style="color: var(--accent-amber); font-size: 1.25rem; flex-shrink: 0; line-height: 1;">
                    <svg style="width: 24px; height: 24px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
                    </svg>
                </div>
                <div>
                    <div style="font-weight: 700; color: var(--accent-amber); font-size: 0.95rem; margin-bottom: 0.25rem;">
                        Rekomendasi: Belum Kompeten (BK) / Dalam Proses Pengujian
                    </div>
                    <div style="font-size: 0.85rem; color: var(--text-secondary); line-height: 1.5;">
                        Hasil uji kompetensi belum memenuhi seluruh unit tugas atau masih memerlukan pengumpulan bukti tugas ujian praktik. Pastikan Anda menyelesaikan seluruh unit tugas ujian di bawah ini dan menunggu validasi asesor.
                    </div>
                </div>
            </div>
        @endif

        <!-- Rincian Biodata Asesi Terverifikasi -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(220px, 1fr)); gap: 1rem; font-size: 0.85rem;">
            <div style="background: #f8fafc; padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                <div style="color: var(--text-muted); font-size: 0.75rem; margin-bottom: 0.25rem;">Nama Lengkap Asesi</div>
                <div style="font-weight: 700; color: var(--text-primary);">{{ $participant->full_name }}</div>
            </div>
            <div style="background: #f8fafc; padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                <div style="color: var(--text-muted); font-size: 0.75rem; margin-bottom: 0.25rem;">Email Terdaftar</div>
                <div style="font-weight: 600; color: var(--text-primary);">{{ $participant->email }}</div>
            </div>
            <div style="background: #f8fafc; padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                <div style="color: var(--text-muted); font-size: 0.75rem; margin-bottom: 0.25rem;">Nomor WhatsApp / HP</div>
                <div style="font-weight: 600; color: var(--text-primary); font-family: var(--font-mono);">{{ $participant->phone_number }}</div>
            </div>
            <div style="background: #f8fafc; padding: 0.85rem 1rem; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                <div style="color: var(--text-muted); font-size: 0.75rem; margin-bottom: 0.25rem;">Tanggal Pendaftaran</div>
                <div style="font-weight: 600; color: var(--text-primary); font-family: var(--font-mono);">{{ $participant->created_at->format('d M Y, H:i') }}</div>
            </div>
        </div>
    </div>

    <!-- Rincian Unit Tugas Ujian Skema & Status Pengerjaan -->
    <div style="background: #ffffff; border: 1px solid var(--border-subtle); border-radius: var(--radius-md); box-shadow: var(--shadow-card); overflow: hidden;">
        <div style="padding: 1.25rem 1.75rem; border-bottom: 1px solid var(--border-subtle); display: flex; justify-content: space-between; align-items: center; background: #ffffff;">
            <div>
                <h3 style="font-size: 1.1rem; font-weight: 700; color: var(--text-primary);">Rincian Unit Tugas Ujian Skema</h3>
                <p style="font-size: 0.8rem; color: var(--text-muted); margin-top: 0.15rem;">Status pemeriksaan bukti pengerjaan untuk setiap unit kompetensi.</p>
            </div>
            <a href="{{ route('lsp.dashboard') }}#tasksSection" class="btn btn-secondary" style="font-size: 0.8rem; padding: 0.4rem 0.85rem;">
                Kumpulkan / Update Tugas &rarr;
            </a>
        </div>

        <table class="astro-table">
            <thead>
                <tr>
                    <th style="width: 140px;">Kode Tugas</th>
                    <th>Judul &amp; Uraian Soal Ujian</th>
                    <th style="width: 180px;">Status Submisi</th>
                    <th>Catatan Review Asesor</th>
                </tr>
            </thead>
            <tbody>
                @forelse($schemeTasks as $task)
                    @php
                        $sub = $task->submissions->first();
                    @endphp
                    <tr>
                        <td>
                            <span class="badge badge-code" style="color: #4338ca;">{{ $task->task_code ?: 'TSK-'.str_pad($task->id, 2, '0', STR_PAD_LEFT) }}</span>
                        </td>
                        <td>
                            <div style="font-weight: 700; color: var(--text-primary); margin-bottom: 0.25rem;">{{ $task->title }}</div>
                            <div style="font-size: 0.8rem; color: var(--text-muted); line-height: 1.45;">{{ $task->description }}</div>
                        </td>
                        <td>
                            @if(!$sub)
                                <span class="badge" style="background: #f1f5f9; color: #64748b; border: 1px solid #cbd5e1;">
                                    Belum Dikumpulkan
                                </span>
                            @elseif($sub->status === 'Diterima')
                                <span class="badge badge-kompeten">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: currentColor;"></span>
                                    Diterima (Lulus)
                                </span>
                            @elseif($sub->status === 'Ditolak')
                                <span class="badge badge-belum">
                                    <span style="width: 6px; height: 6px; border-radius: 50%; background: currentColor;"></span>
                                    Ditolak (Perlu Revisi)
                                </span>
                            @else
                                <span class="badge" style="background: #fef3c7; color: #b45309; border: 1px solid #fde68a;">
                                    Menunggu Peninjauan
                                </span>
                            @endif
                        </td>
                        <td>
                            @if($sub && $sub->admin_notes)
                                <div style="font-size: 0.825rem; color: var(--text-primary); background: #f8fafc; padding: 0.5rem 0.75rem; border-radius: var(--radius-sm); border: 1px solid var(--border-subtle);">
                                    {{ $sub->admin_notes }}
                                </div>
                            @elseif($sub)
                                <span style="font-size: 0.8rem; color: var(--text-dim); font-style: italic;">Belum ada catatan khusus asesor.</span>
                            @else
                                <span style="font-size: 0.8rem; color: var(--text-dim);">-</span>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="4" style="text-align: center; color: var(--text-muted); padding: 3rem 1.5rem;">
                            <div style="font-weight: 600;">Belum ada unit tugas ujian untuk skema ini</div>
                            <div style="font-size: 0.8rem; margin-top: 0.25rem;">Administrator belum menambahkan unit soal ujian untuk skema sertifikasi Anda.</div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

@else
    <!-- Empty State: Belum Terdaftar Pada Skema -->
    <div style="background: #ffffff; border: 1px solid var(--border-subtle); border-radius: var(--radius-md); padding: 3.5rem 2rem; text-align: center; box-shadow: var(--shadow-card);">
        <div style="width: 54px; height: 54px; border-radius: 50%; background: #eef2ff; color: #4338ca; display: grid; place-items: center; margin: 0 auto 1.25rem;">
            <svg style="width: 28px; height: 28px;" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v3.75m9-.75a9 9 0 11-18 0 9 9 0 0118 0zm-9 3.75h.008v.008H12v-.008z" />
            </svg>
        </div>
        <h3 style="font-size: 1.25rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.5rem;">
            Data Asesi Belum Terdaftar Pada Skema
        </h3>
        <p style="font-size: 0.875rem; color: var(--text-muted); max-width: 520px; margin: 0 auto 1.5rem; line-height: 1.5;">
            Hasil rekomendasi dan status asesmen hanya tersedia bagi asesi yang telah terdaftar pada salah satu skema sertifikasi LSP. Silakan pilih skema pada dashboard utama Anda.
        </p>
        <a href="{{ route('lsp.dashboard') }}" class="btn btn-primary">
            Ke Dashboard &amp; Pilih Skema &rarr;
        </a>
    </div>
@endif
@endsection
