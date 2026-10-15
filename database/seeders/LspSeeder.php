<?php

namespace Database\Seeders;

use App\Models\AssessmentTask;
use App\Models\CertificationScheme;
use App\Models\LspReportSetting;
use App\Models\Participant;
use App\Models\TaskSubmission;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

/**
 * Seeder: LspSeeder
 * UNTUK APA: Mengisi data permulaan (dummy/default) ke dalam database untuk keperluan pengujian sistem.
 */
class LspSeeder extends Seeder
{
    /**
     * FUNGSI: run()
     * UNTUK APA: Menjalankan proses injeksi data awal (Admin, Skema Sertifikasi, Asesi, dan Template Laporan USK) ke database.
     * ALUR KERJA:
     * 1. Menambahkan user admin default (admin@lsp.id / admin123).
     * 2. Menambahkan 3 data master skema sertifikasi standar BNSP.
     * 3. Menambahkan 3 data peserta asesmen dengan status kelulusan berbeda.
     * 4. Menambahkan 1 template konfigurasi cetak laporan Berita Acara USK standar.
     */
    public function run(): void
    {
        // 1. Injeksi User Administrator & Sample Asesi (User)
        User::where('email', 'admin@lsp.id')->delete();

        User::updateOrCreate(
            ['email' => 'admin@gmail.com'],
            [
                'name' => 'admin',
                'password' => Hash::make('123456'),
                'role' => 'admin',
                'phone_number' => '081299990001',
            ]
        );

        User::updateOrCreate(
            ['email' => 'budi.santoso@example.com'],
            [
                'name' => 'Budi Santoso',
                'nik' => '3171012304900001',
                'gender' => 'Laki-laki',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'phone_number' => '081234567890',
            ]
        );

        User::updateOrCreate(
            ['email' => 'dewi.lestari@example.com'],
            [
                'name' => 'Dewi Lestari',
                'nik' => '3271016509950004',
                'gender' => 'Perempuan',
                'password' => Hash::make('user123'),
                'role' => 'user',
                'phone_number' => '081388776655',
            ]
        );

        // 2. Injeksi Data Master Skema Sertifikasi
        $s1 = CertificationScheme::firstOrCreate(
            ['scheme_code' => 'JWD-2026'],
            [
                'scheme_name' => 'Junior Web Developer',
                'description' => 'Standar okupasi nasional bidang pemrograman terstruktur dan antarmuka web responsif.'
            ]
        );

        $s2 = CertificationScheme::firstOrCreate(
            ['scheme_code' => 'DMA-2026'],
            [
                'scheme_name' => 'Digital Marketing Analyst',
                'description' => 'Skema keahlian analitik data promosi dan strategi digital marketing.'
            ]
        );

        $s3 = CertificationScheme::firstOrCreate(
            ['scheme_code' => 'NSA-2026'],
            [
                'scheme_name' => 'Network System Administrator',
                'description' => 'Standar kompetensi pengelolaan infrastruktur jaringan, server, dan keamanan sistem informasi.'
            ]
        );

        // 3. Injeksi Data Asesi / Peserta Uji Kompetensi
        Participant::firstOrCreate(
            ['registration_number' => 'REG-2026-001'],
            [
                'scheme_id' => $s1->id,
                'nik' => '3171012304900001',
                'gender' => 'Laki-laki',
                'full_name' => 'Budi Santoso',
                'email' => 'budi.santoso@example.com',
                'phone_number' => '081234567890',
                'address' => 'Jl. Merdeka No. 10, Jakarta Pusat',
                'status' => 'Kompeten',
            ]
        );

        Participant::firstOrCreate(
            ['registration_number' => 'REG-2026-002'],
            [
                'scheme_id' => $s1->id,
                'nik' => '3273014508920002',
                'gender' => 'Perempuan',
                'full_name' => 'Siti Rahmawati',
                'email' => 'siti.rahma@example.com',
                'phone_number' => '082198765432',
                'address' => 'Jl. Diponegoro No. 22, Bandung',
                'status' => 'Belum Kompeten',
            ]
        );

        Participant::firstOrCreate(
            ['registration_number' => 'REG-2026-003'],
            [
                'scheme_id' => $s2->id,
                'nik' => '3578011206890003',
                'gender' => 'Laki-laki',
                'full_name' => 'Ahmad Fauzi',
                'email' => 'ahmad.fauzi@example.com',
                'phone_number' => '085712345678',
                'address' => 'Jl. Gajah Mada No. 45, Surabaya',
                'status' => 'Kompeten',
            ]
        );

        Participant::firstOrCreate(
            ['registration_number' => 'REG-2026-004'],
            [
                'scheme_id' => $s1->id,
                'nik' => '3271016509950004',
                'gender' => 'Perempuan',
                'full_name' => 'Dewi Lestari',
                'email' => 'dewi.lestari@example.com',
                'phone_number' => '081388776655',
                'address' => 'Jl. Pajajaran No. 88, Bogor',
                'status' => 'Pending',
            ]
        );

        // 4. Injeksi Konfigurasi Template Cetak Berita Acara USK (CRUD Dokumen)
        LspReportSetting::firstOrCreate(
            ['template_name' => 'Standar LSP USK'],
            [
                'institution_name' => 'LEMBAGA SERTIFIKASI PROFESI (LSP) TEKNOLOGI INFORMASI',
                'tuk_name' => 'TEMPAT UJI KOMPETENSI (TUK) MANDIRI KOTA JAKARTA TIMUR',
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

        // 5. Injeksi Master Unit Tugas Ujian Asesmen
        $t1 = AssessmentTask::firstOrCreate(
            ['task_code' => 'TSK-JWD-01'],
            [
                'scheme_id' => $s1->id,
                'title' => 'Implementasi CRUD & Validasi Input Web',
                'description' => 'Buat modul entri data berbasis form dengan validasi server-side dan penanganan error responsif sesuai unit kompetensi J.620100.017.02.',
            ]
        );

        $t2 = AssessmentTask::firstOrCreate(
            ['task_code' => 'TSK-JWD-02'],
            [
                'scheme_id' => $s1->id,
                'title' => 'Proteksi Keamanan Basis Data & Otentikasi',
                'description' => 'Implementasikan hashing password aman dan proteksi injeksi SQL / XSS pada sesi login sesuai unit kompetensi J.620100.023.02.',
            ]
        );

        $t3 = AssessmentTask::firstOrCreate(
            ['task_code' => 'TSK-DMA-01'],
            [
                'scheme_id' => $s2->id,
                'title' => 'Audit Metrik Konversi & Strategi Promosi Digital',
                'description' => 'Susun laporan ringkas analitik konversi kampanye digital dan target ROI berdasarkan studi kasus UKM binaan.',
            ]
        );

        $t4 = AssessmentTask::firstOrCreate(
            ['task_code' => 'TSK-NSA-01'],
            [
                'scheme_id' => $s3->id,
                'title' => 'Konfigurasi Layanan Jaringan & Pengujian Keamanan',
                'description' => 'Konfigurasikan alokasi IP, gateway, serta firewall rules untuk mencegah unauthorized access pada server lokal.',
            ]
        );
    }
}
