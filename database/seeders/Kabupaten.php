<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Kabupaten extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $kabupatens = [
            [
                'id_prov' => 1,
                'nama_kab' => 'Kabupaten Sumenep'
            ],[
                'id_prov' => 1,
                'nama_kab' => 'Kabupaten Pamekasan'
            ],[
                'id_prov' => 1,
                'nama_kab' => 'Kabupaten Sampang'
            ],[
                'id_prov' => 1,
                'nama_kab' => 'Kabupaten Bangkalan'
            ],[
                'id_prov' => 2,
                'nama_kab' => 'Kota Semarang'
            ],[
                'id_prov' => 2,
                'nama_kab' => 'Kabupaten Pati'
            ],[
                'id_prov' => 2,
                'nama_kab' => 'Kabupaten Boyolali'
            ],[
                'id_prov' => 3,
                'nama_kab' => 'Kabupaten Cirebon'
            ],[
                'id_prov' => 3,
                'nama_kab' => 'Kabupaten Ciamis'
            ],[
                'id_prov' => 3,
                'nama_kab' => 'Kabupaten Indramayu'
            ],
        ];
        foreach ($kabupatens as $kabupaten) {
            \App\Models\Kabupaten::create($kabupaten);
        }
    }
}
