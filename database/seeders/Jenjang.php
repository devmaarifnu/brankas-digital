<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Jenjang as JenjangModel;

class Jenjang extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenjang = [
            [
              "nm_jenjang" => 'PAUD',
              "keterangan"  => 'Pendidikan Anak Usia Dini',
            ],[
                "nm_jenjang" => 'KB',
                "keterangan"  => 'Kelompok Bermain',
            ],[
                "nm_jenjang" => 'TPQ',
                "keterangan"  => 'Taman Pendidikan Qur`an',
            ],[
                "nm_jenjang" => 'RA',
                "keterangan"  => 'Raudataul Atfal',
            ],[
                "nm_jenjang" => 'TK',
                "keterangan"  => 'Taman Kanak',
            ],[
                "nm_jenjang" => 'SD',
                "keterangan"  => 'Sekolah Dasar',
            ],[
                "nm_jenjang" => 'MI',
                "keterangan"  => 'Madrasah Ibtidaiyah',
            ],[
                "nm_jenjang" => 'SMP',
                "keterangan"  => 'Sekolah Menengah Pertama',
            ],[
                "nm_jenjang" => 'MTS',
                "keterangan"  => 'Madrasah Stanawiyah',
            ],[
                "nm_jenjang" => 'SMA',
                "keterangan"  => 'Sekolah Menengah Atas',
            ],[
                "nm_jenjang" => 'SMK',
                "keterangan"  => 'Sekolah Menengah Kejuruan',
            ],[
                "nm_jenjang" => 'MA',
                "keterangan"  => 'Madrasah Aliyah',
            ],[
                "nm_jenjang" => 'MD',
                "keterangan"  => 'Madrasah Diniyah',
            ],[
                "nm_jenjang" => 'SLB',
                "keterangan"  => 'Sekolah Luar Biasa',
            ],[
                "nm_jenjang" => 'PT',
                "keterangan"  => 'Perguruan Tinggi',
            ],

        ];

        foreach ($jenjang as $row) {
            JenjangModel::create(['nm_jenjang' => $row['nm_jenjang'], 'keterangan' => $row['keterangan']]);
        }
    }
}
