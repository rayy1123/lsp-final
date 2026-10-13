<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

/**
 * Seeder: DatabaseSeeder
 * UNTUK APA: Titik masuk (entry point) utama pemanggilan seluruh seeder aplikasi saat menjalankan 'php artisan db:seed'.
 */
class DatabaseSeeder extends Seeder
{
    /**
     * FUNGSI: run()
     * UNTUK APA: Mendaftarkan dan mengeksekusi kelas seeder modul yang ingin dijalankan secara berurutan.
     * ALUR KERJA: Memanggil LspSeeder::class untuk menginjeksi data pengguna, skema, dan peserta.
     */
    public function run(): void
    {
        $this->call([
            LspSeeder::class,
        ]);
    }
}
