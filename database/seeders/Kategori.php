<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Kategori extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kategori = [
            [
                'nm_kategori' => 'A',
                'konotasi' => "yayasan bhpnu dan tanah milik nu (jami'yah)",
                'keterangan' => "Dan memberikan izin kepada satuan pendidikan tersebut untuk menyelenggarakan pendidikan menggunakan Akta Perkumpulan Nahdlatul Ulama (BHPNU).",
            ],
            [
                'nm_kategori' => 'B',
                'konotasi' => "yayasan bhpnu dan tanah milik masyarakat nu",
                'keterangan' => "Dan memberikan izin kepada satuan pendidikan tersebut untuk menyelenggarakan pendidikan menggunakan Akta Perkumpulan Nahdlatul Ulama (BHPNU).",
            ],
            [
                'nm_kategori' => 'C',
                'konotasi' => "yayasan non bhpnu dan tanah milik nu (jam'iyah)",
                'keterangan' => "",
            ],
            [
                'nm_kategori' => 'D',
                'konotasi' => "yayasan non bhpnu dan tanah milik masyarakat nu",
                'keterangan' => "",
            ],
        ];
        foreach ($kategori as $row) {
            \App\Models\Kategori::create($row);
        }
    }
}
