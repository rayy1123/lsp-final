<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * Model: LspReportSetting
 * Tabel: lsp_report_settings
 * Menyimpan konfigurasi konten kop surat, nomor dokumen, tanggal, TUK, metode asesmen, dan penandatangan Berita Acara USK.
 */
class LspReportSetting extends Model
{
    use HasFactory;

    /**
     * Properti: $fillable
     * Kolom tabel yang diizinkan untuk mass-assignment CRUD.
     */
    protected $fillable = [
        'template_name',
        'institution_name',
        'tuk_name',
        'address',
        'contact_info',
        'document_title',
        'document_number',
        'tuk_location',
        'assessment_date',
        'assessment_method',
        'closing_statement',
        'signer1_role',
        'signer1_name',
        'signer1_reg_number',
        'signer2_city',
        'signer2_role',
        'signer2_name',
        'signer2_reg_number',
        'is_active',
    ];

    /**
     * Properti: $casts
     * Konversi tipe data otomatis.
     */
    protected $casts = [
        'is_active' => 'boolean',
        'assessment_date' => 'date',
    ];

    /**
     * FUNGSI: getActiveOrDefault()
     * UNTUK APA: Mengambil konfigurasi laporan yang sedang aktif, atau membuat data default jika tabel masih kosong.
     * ALUR KERJA: Cek record pertama berstatus is_active = true, jika tidak ada fallback ke firstOrCreate record default.
     */
    public static function getActiveOrDefault()
    {
        return self::where('is_active', true)->first() ?? self::firstOrCreate(
            ['template_name' => 'Standar LSP USK'],
            [
                'institution_name' => 'LEMBAGA SERTIFIKASI PROFESI (LSP) TEKNOLOGI INFORMASI',
                'tuk_name' => 'TEMPAT UJI KOMPETENSI (TUK) MANDIRI KOTA BANDA ACEH',
                'address' => 'Gedung ICT Center, Jl. Syech Abdurrauf No. 7, Darussalam, Banda Aceh',
                'contact_info' => 'Email: sekretariat@lsp.id | Website: https://lsp.usk.ac.id | Telp: (0651) 7553205',
                'document_title' => 'BERITA ACARA & LAPORAN PELAKSANAAN UJI SERTIFIKASI KOMPETENSI (USK)',
                'document_number' => 'BA-USK/' . date('Y') . '/LSP-TI/001',
                'tuk_location' => 'TUK Lab Pemrograman Komputer USK',
                'assessment_date' => now(),
                'assessment_method' => 'Demonstrasi Praktik & Tes Tertulis',
                'closing_statement' => 'Demikian Berita Acara dan Laporan Hasil Uji Sertifikasi Kompetensi ini dibuat dengan sebenar-benarnya berdasarkan bukti-bukti asesmen yang valid, otentik, terkini, dan memadai.',
                'signer1_role' => 'Ketua / Direktur LSP',
                'signer1_name' => 'Dr. Ir. T. Fachruddin, M.T.',
                'signer1_reg_number' => 'MET.000.002847.2021',
                'signer2_city' => 'Banda Aceh',
                'signer2_role' => 'Asesor Penguji Kompetensi',
                'signer2_name' => 'M. Nur Maulana, S.Kom., M.Cs.',
                'signer2_reg_number' => 'MET.000.009384.2023',
                'is_active' => true,
            ]
        );
    }
}
