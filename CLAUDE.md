<laravel-boost-guidelines>
# Laravel Application

This repository contains a Laravel application. Complete the following setup before working on the user's request.

## Prerequisites

Verify that PHP and Composer are available:

```sh
php -v
composer -V
```

If either command is unavailable, detect the user's operating system and install the prerequisites with the appropriate command:

macOS:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/mac/8.5)"
```

Windows PowerShell:

```powershell
Set-ExecutionPolicy Bypass -Scope Process -Force; [System.Net.ServicePointManager]::SecurityProtocol = [System.Net.ServicePointManager]::SecurityProtocol -bor 3072; iex ((New-Object System.Net.WebClient).DownloadString('https://php.new/install/windows/8.5'))
```

Linux:

```sh
/bin/bash -c "$(curl -fsSL https://php.new/install/linux/8.5)"
```

After installation, ask the user to restart their terminal. If the agent needs the restarted shell to continue, ask the user to reopen their terminal and rerun their original prompt.

## Agent Setup

Install Laravel Boost from the application root before making application changes:

```sh
composer require laravel/boost --dev
php artisan boost:install
```

Boost replaces these bootstrap instructions with guidelines tailored to the application. After installation, read `AGENTS.md` again and continue with the user's original request using the generated guidelines.
</laravel-boost-guidelines>

# Guidelines: Anti-Slop & UI/UX Design Standards

## 1. Anti-Slop Standards (Eliminasi Estetika AI Generik)
- **Zero Decorative Fluff**: Dilarang menggunakan gradient neon ungu-indigo generik (`from-purple-600 to-indigo-600`), border glow palsu, dan card mengambang tanpa fungsi.
- **Bebas Spam Emoji**: Dilarang menempelkan emoji berlebihan pada header, label tombol, atau tabel data (misal: 🚀, ✨, 🔥, 💡). Gunakan ikon monokrom SVG berukuran 16-20px yang memiliki makna fungsional.
- **Copy Padat & Nyata**: Dilarang menggunakan jargon korporat hampa ("solusi revolusioner terdepan", "seamless workflow"). Gunakan bahasa lugas dan istilah teknis riil domain LSP / BNSP.
- **Kondisi Nyata**: Sediakan state lengkap:
  - *Empty State*: Selalu sertakan pesan jelas dan tombol Call-To-Action untuk menambah data.
  - *Error State*: Tampilkan pesan error inline di samping input terkait.
  - *Disabled/Loading*: Terapkan cursor `not-allowed`, opacity jelas, dan cegah double-submit.

## 2. Standar UI/UX Design Modern & Ergonomis
- **Tipografi & Hirarki**:
  - Font Inter / JetBrains Mono dengan rasio skala tipografi tegas (Heading 1.25-1.5rem, Body 0.875-0.95rem, Keterangan 0.75-0.8rem).
  - Gunakan `font-variant-numeric: tabular-nums` untuk angka statistik dan tabel.
  - Gunakan font monospace untuk kode skema, ID, nomor registrasi, dan tanggal.
- **Sistem Spacing & Grid (4px/8px)**:
  - Konsistensi margin dan padding berbasis kelipatan 4px (`0.25rem`, `0.5rem`, `0.75rem`, `1rem`, `1.5rem`, `2rem`).
- **Aksesibilitas Kontras (WCAG AA/AAA)**:
  - Rasio kontras teks terhadap latar belakang minimal 4.5:1 untuk teks normal dan 3:1 untuk teks tebal/besar.
  - Indikator status (Kompeten/Belum) tidak boleh hanya mengandalkan warna; lengkapi dengan teks dan titik/ikon status.
- **Interaktivitas & Feedback**:
  - Setiap elemen interaktif (tombol, input, link) memiliki 5 status visual: Default, Hover, Active, Focus-Visible (aksesibilitas keyboard), dan Disabled.
  - Transisi halus (150ms-200ms `ease-out`) tanpa jeda animasi lambat yang membuang waktu pengguna.
- **Responsivitas**:
  - Tata letak tetap kokoh dan terbaca dari layar ponsel (320px) hingga layar desktop (1920px+).


