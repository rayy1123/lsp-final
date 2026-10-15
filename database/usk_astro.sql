-- ============================================================
-- SQL DDL & DML: SISTEM SERTIFIKASI LSP USK (LARAVEL ASTRO)
-- Database Name: usk_astro
-- ============================================================

CREATE DATABASE IF NOT EXISTS `usk_astro` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `usk_astro`;

-- 1. TABEL USERS (Autentikasi Administrator & Asesi/User)
CREATE TABLE IF NOT EXISTS `users` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `nik` varchar(16) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `education_type` varchar(50) NOT NULL DEFAULT 'Siswa/Pelajar',
  `academic_title` varchar(50) DEFAULT NULL,
  `email` varchar(255) NOT NULL,
  `role` varchar(50) NOT NULL DEFAULT 'user',
  `phone_number` varchar(20) DEFAULT NULL,
  `address` text DEFAULT NULL,
  `id_card_file` varchar(255) DEFAULT NULL,
  `report_sheet_file` varchar(255) DEFAULT NULL,
  `certificate_file` varchar(255) DEFAULT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `remember_token` varchar(100) DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 2. TABEL SESSIONS (Dibutuhkan untuk session database Laravel)
CREATE TABLE IF NOT EXISTS `sessions` (
  `id` varchar(255) NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `ip_address` varchar(45) DEFAULT NULL,
  `user_agent` text DEFAULT NULL,
  `payload` longtext NOT NULL,
  `last_activity` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 3. TABEL CACHE
CREATE TABLE IF NOT EXISTS `cache` (
  `key` varchar(255) NOT NULL,
  `value` mediumtext NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

CREATE TABLE IF NOT EXISTS `cache_locks` (
  `key` varchar(255) NOT NULL,
  `owner` varchar(255) NOT NULL,
  `expiration` int(11) NOT NULL,
  PRIMARY KEY (`key`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 4. TABEL MASTER SKEMA SERTIFIKASI (certification_schemes)
CREATE TABLE IF NOT EXISTS `certification_schemes` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `scheme_code` varchar(255) NOT NULL,
  `scheme_name` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `certification_schemes_scheme_code_unique` (`scheme_code`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 5. TABEL PESERTA ASESMEN / ASESI (participants)
CREATE TABLE IF NOT EXISTS `participants` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `scheme_id` bigint(20) unsigned NOT NULL,
  `registration_number` varchar(255) NOT NULL,
  `nik` varchar(16) DEFAULT NULL,
  `gender` varchar(20) DEFAULT NULL,
  `education_type` varchar(50) NOT NULL DEFAULT 'Siswa/Pelajar',
  `academic_title` varchar(50) DEFAULT NULL,
  `full_name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `phone_number` varchar(20) NOT NULL,
  `address` text DEFAULT NULL,
  `id_card_file` varchar(255) DEFAULT NULL,
  `report_sheet_file` varchar(255) DEFAULT NULL,
  `certificate_file` varchar(255) DEFAULT NULL,
  `status` varchar(255) NOT NULL DEFAULT 'Pending',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `participants_registration_number_unique` (`registration_number`),
  KEY `participants_scheme_id_foreign` (`scheme_id`),
  CONSTRAINT `participants_scheme_id_foreign` FOREIGN KEY (`scheme_id`) REFERENCES `certification_schemes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 6. TABEL KONFIGURASI CETAK LAPORAN / BERITA ACARA USK (lsp_report_settings)
-- Mendukung CRUD template & teks isi dokumen cetak secara dinamis
CREATE TABLE IF NOT EXISTS `lsp_report_settings` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `template_name` varchar(255) NOT NULL DEFAULT 'Standar LSP',
  `institution_name` varchar(255) NOT NULL,
  `tuk_name` varchar(255) NOT NULL,
  `address` text NOT NULL,
  `contact_info` text NOT NULL,
  `document_title` varchar(255) NOT NULL,
  `document_number` varchar(255) NOT NULL,
  `tuk_location` varchar(255) NOT NULL,
  `assessment_date` date DEFAULT NULL,
  `assessment_method` varchar(255) NOT NULL,
  `closing_statement` text NOT NULL,
  `signer1_role` varchar(255) NOT NULL,
  `signer1_name` varchar(255) NOT NULL,
  `signer1_reg_number` varchar(255) DEFAULT NULL,
  `signer2_city` varchar(255) NOT NULL,
  `signer2_role` varchar(255) NOT NULL,
  `signer2_name` varchar(255) NOT NULL,
  `signer2_reg_number` varchar(255) DEFAULT NULL,
  `is_active` tinyint(1) NOT NULL DEFAULT 1,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 7. TABEL UNIT TUGAS / SKEMA UJIAN (assessment_tasks)
CREATE TABLE IF NOT EXISTS `assessment_tasks` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `scheme_id` bigint(20) unsigned NOT NULL,
  `task_code` varchar(50) DEFAULT NULL,
  `title` varchar(255) NOT NULL,
  `description` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `assessment_tasks_scheme_id_foreign` (`scheme_id`),
  CONSTRAINT `assessment_tasks_scheme_id_foreign` FOREIGN KEY (`scheme_id`) REFERENCES `certification_schemes` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- 8. TABEL SUBMISI / BUKTI PENYELESAIAN TUGAS (task_submissions)
CREATE TABLE IF NOT EXISTS `task_submissions` (
  `id` bigint(20) unsigned NOT NULL AUTO_INCREMENT,
  `task_id` bigint(20) unsigned NOT NULL,
  `participant_id` bigint(20) unsigned NOT NULL,
  `user_id` bigint(20) unsigned DEFAULT NULL,
  `submission_content` text NOT NULL,
  `file_path` varchar(255) DEFAULT NULL,
  `file_name` varchar(255) DEFAULT NULL,
  `file_type` varchar(50) DEFAULT NULL,
  `status` varchar(50) NOT NULL DEFAULT 'Menunggu Peninjauan',
  `is_revised` tinyint(1) NOT NULL DEFAULT 0,
  `revision_count` int(11) NOT NULL DEFAULT 0,
  `admin_notes` text DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `task_submissions_task_id_foreign` (`task_id`),
  KEY `task_submissions_participant_id_foreign` (`participant_id`),
  KEY `task_submissions_user_id_foreign` (`user_id`),
  CONSTRAINT `task_submissions_task_id_foreign` FOREIGN KEY (`task_id`) REFERENCES `assessment_tasks` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_submissions_participant_id_foreign` FOREIGN KEY (`participant_id`) REFERENCES `participants` (`id`) ON DELETE CASCADE,
  CONSTRAINT `task_submissions_user_id_foreign` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- ============================================================
-- DML: DATA AWAL (SEEDING)
-- ============================================================

-- Data Admin (admin@gmail.com / Password: 123456) & Sample Users
DELETE FROM `users` WHERE `email` = 'admin@lsp.id';
INSERT INTO `users` (`id`, `name`, `email`, `role`, `phone_number`, `password`, `created_at`, `updated_at`)
VALUES
(1, 'admin', 'admin@gmail.com', 'admin', '081299990001', '$2y$12$4L6i4x3h3y.kI4Hh1Qv6kOCw9zGk.gM9R4rO7C4p3c7Q9aG8zO7re', NOW(), NOW()),
(2, 'Budi Santoso', 'budi.santoso@example.com', 'user', '081234567890', '$2y$12$4L6i4x3h3y.kI4Hh1Qv6kOCw9zGk.gM9R4rO7C4p3c7Q9aG8zO7re', NOW(), NOW()),
(3, 'Dewi Lestari', 'dewi.lestari@example.com', 'user', '081388776655', '$2y$12$4L6i4x3h3y.kI4Hh1Qv6kOCw9zGk.gM9R4rO7C4p3c7Q9aG8zO7re', NOW(), NOW())
ON DUPLICATE KEY UPDATE `email` = VALUES(`email`), `role` = VALUES(`role`);

-- Data Master Skema Sertifikasi
INSERT INTO `certification_schemes` (`id`, `scheme_code`, `scheme_name`, `description`, `created_at`, `updated_at`) VALUES
(1, 'JWD-2026', 'Junior Web Developer', 'Standar okupasi nasional bidang pemrograman terstruktur dan antarmuka web responsif.', NOW(), NOW()),
(2, 'DMA-2026', 'Digital Marketing Analyst', 'Skema keahlian analitik data promosi dan strategi digital marketing.', NOW(), NOW()),
(3, 'NSA-2026', 'Network System Administrator', 'Standar kompetensi pengelolaan infrastruktur jaringan, server, dan keamanan sistem informasi.', NOW(), NOW())
ON DUPLICATE KEY UPDATE `scheme_name` = VALUES(`scheme_name`);

-- Data Peserta Asesmen (Asesi)
INSERT INTO `participants` (`id`, `scheme_id`, `registration_number`, `nik`, `gender`, `full_name`, `email`, `phone_number`, `address`, `status`, `created_at`, `updated_at`) VALUES
(1, 1, 'REG-2026-001', '3171012304900001', 'Laki-laki', 'Budi Santoso', 'budi.santoso@example.com', '081234567890', 'Jl. Merdeka No. 10, Jakarta Pusat', 'Kompeten', NOW(), NOW()),
(2, 1, 'REG-2026-002', '3273014508920002', 'Perempuan', 'Siti Rahmawati', 'siti.rahma@example.com', '082198765432', 'Jl. Diponegoro No. 22, Bandung', 'Belum Kompeten', NOW(), NOW()),
(3, 2, 'REG-2026-003', '3578011206890003', 'Laki-laki', 'Ahmad Fauzi', 'ahmad.fauzi@example.com', '085712345678', 'Jl. Gajah Mada No. 45, Surabaya', 'Kompeten', NOW(), NOW()),
(4, 1, 'REG-2026-004', '3271016509950004', 'Perempuan', 'Dewi Lestari', 'dewi.lestari@example.com', '081388776655', 'Jl. Pajajaran No. 88, Bogor', 'Pending', NOW(), NOW())
ON DUPLICATE KEY UPDATE `full_name` = VALUES(`full_name`), `status` = VALUES(`status`);

-- Data Konfigurasi Cetak Laporan / Berita Acara USK Default
INSERT INTO `lsp_report_settings` (`id`, `template_name`, `institution_name`, `tuk_name`, `address`, `contact_info`, `document_title`, `document_number`, `tuk_location`, `assessment_date`, `assessment_method`, `closing_statement`, `signer1_role`, `signer1_name`, `signer1_reg_number`, `signer2_city`, `signer2_role`, `signer2_name`, `signer2_reg_number`, `is_active`, `created_at`, `updated_at`) VALUES
(1, 'Standar LSP USK', 'LEMBAGA SERTIFIKASI PROFESI (LSP) TEKNOLOGI INFORMASI', 'TEMPAT UJI KOMPETENSI (TUK) MANDIRI KOTA BANDA ACEH', 'Gedung ICT Center, Jl. Syech Abdurrauf No. 7, Darussalam, Banda Aceh', 'Email: sekretariat@lsp.id | Website: https://lsp.usk.ac.id | Telp: (0651) 7553205', 'BERITA ACARA & LAPORAN PELAKSANAAN UJI SERTIFIKASI KOMPETENSI (USK)', 'BA-USK/2026/LSP-TI/001', 'TUK Lab Pemrograman Komputer USK', '2026-10-13', 'Demonstrasi Praktik & Tes Tertulis', 'Demikian Berita Acara dan Laporan Hasil Uji Sertifikasi Kompetensi ini dibuat dengan sebenar-benarnya berdasarkan bukti-bukti asesmen yang valid, otentik, terkini, dan memadai.', 'Ketua / Direktur LSP', 'Dr. Ir. T. Fachruddin, M.T.', 'MET.000.002847.2021', 'Banda Aceh', 'Asesor Penguji Kompetensi', 'M. Nur Maulana, S.Kom., M.Cs.', 'MET.000.009384.2023', 1, NOW(), NOW())
ON DUPLICATE KEY UPDATE `template_name` = VALUES(`template_name`);

-- Data Tugas Ujian / Asesmen Praktik
INSERT INTO `assessment_tasks` (`id`, `scheme_id`, `task_code`, `title`, `description`, `created_at`, `updated_at`) VALUES
(1, 1, 'TSK-JWD-01', 'Implementasi CRUD & Validasi Input Web', 'Buat modul entri data berbasis form dengan validasi server-side dan penanganan error responsif sesuai unit kompetensi J.620100.017.02.', NOW(), NOW()),
(2, 1, 'TSK-JWD-02', 'Proteksi Keamanan Basis Data & Otentikasi', 'Implementasikan hashing password aman dan proteksi injeksi SQL / XSS pada sesi login sesuai unit kompetensi J.620100.023.02.', NOW(), NOW()),
(3, 2, 'TSK-DMA-01', 'Audit Metrik Konversi & Strategi Promosi Digital', 'Susun laporan ringkas analitik konversi kampanye digital dan target ROI berdasarkan studi kasus UKM binaan.', NOW(), NOW()),
(4, 3, 'TSK-NSA-01', 'Konfigurasi Layanan Jaringan & Pengujian Keamanan', 'Konfigurasikan alokasi IP, gateway, serta firewall rules untuk mencegah unauthorized access pada server lokal.', NOW(), NOW())
ON DUPLICATE KEY UPDATE `title` = VALUES(`title`);
