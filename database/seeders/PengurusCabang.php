<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PengurusCabang extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $pcnu = [
            [
                'id_prov' => 1,
                'kode_kab' => '42',
                'nama_pc' => 'Kabupaten Sumenep'
            ],[
                'id_prov' => 1,
                'kode_kab' => '38',
                'nama_pc' => 'Kabupaten Pamekasan'
            ],[
                'id_prov' => 1,
                'kode_kab' => '21',
                'nama_pc' => 'Kabupaten Sampang'
            ],[
                'id_prov' => 1,
                'kode_kab' => '28',
                'nama_pc' => 'Kabupaten Bangkalan'
            ],[
                'id_prov' => 2,
                'kode_kab' => '11',
                'nama_pc' => 'Kota Semarang'
            ],[
                'id_prov' => 2,
                'kode_kab' => '12',
                'nama_pc' => 'Kabupaten Pati'
            ],[
                'id_prov' => 2,
                'kode_kab' => '17',
                'nama_pc' => 'Kabupaten Boyolali'
            ],[
                'id_prov' => 3,
                'kode_kab' => '08',
                'nama_pc' => 'Kabupaten Ciamis'
            ],[
                'id_prov' => 3,
                'kode_kab' => '09',
                'nama_pc' => 'Kabupaten Cirebon'
            ],[
                'id_prov' => 3,
                'kode_kab' => '03',
                'nama_pc' => 'Kabupaten Indramayu'
            ],
        ];
        foreach ($pcnu as $row) {
            \App\Models\PengurusCabang::create($row);
        }
    }
}
