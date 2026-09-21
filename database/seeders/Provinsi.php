<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class Provinsi extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $provinsi = [
            [
                'kode_prov' => '1',
                'nm_prov' => 'Jawa Timur'
            ],
            [
                'kode_prov' => '2',
                'nm_prov' => 'Jawa Tengah'
            ],
            [
                'kode_prov' => '10',
                'nm_prov' => 'Jawa Barat'
            ]
        ];
        foreach ($provinsi as $row) {
            \App\Models\Provinsi::create($row);
        }
    }
}
