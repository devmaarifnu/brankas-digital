<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Date;

class Satpen extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        for($i=0; $i < 5000; $i++) {
            \App\Models\Satpen::create([
                'id_user' => 3,
                'id_prov' => 1,
                'id_kab' => 2,
                'id_pc' => 2,
                'id_kategori' => 3,
                'id_jenjang' => 3,
                'npsn' => str_pad(mt_rand(0, 99999), 5, '0', STR_PAD_LEFT),
                'no_registrasi' => "A". str_pad(mt_rand(0, 99999), 7, '0', STR_PAD_LEFT),
                'nm_satpen' => "SMKN 1 PAMEKASAN",
                'yayasan' => "bhpnu",
                'kepsek' => "Dr. Faisol",
                'telpon' => "082335685138",
                'email' => "faisolofficial99@gmail.com",
                'fax' => "1223222",
                'thn_berdiri' => "2023",
                'alamat' => "Jalan Kurma No. 25",
                'kelurahan' => "Meddelan",
                'kecamatan' => "Lenteng",
                'aset_tanah' => "jamiyah",
                "status" => "setujui",
                'nm_pemilik' => "Faisol",
                'tgl_registrasi' => Date::now(),
            ]);
        }

    }
}
