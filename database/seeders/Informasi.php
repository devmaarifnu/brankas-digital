<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Informasi as InformasiModel;
use Illuminate\Support\Facades\Date;

class Informasi extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $jenjang = [
            [
              "type" => "SK",
                "slug" => "slug-example1",
              "headline" => "Peluncuran Aplikasi Siapin LP Ma'arif",
              "tgl_upload" => Date::now(),
              "content" => "peluncuran informasi",
              "image" => "s4.jpg",
              "tag" => "#halo #test"
            ],[
              "type" => "SK",
                "slug" => "slug-example2",
              "headline" => "Revamp Siapin LP Ma'arif",
              "tgl_upload" => Date::now(),
              "content" => "peluncuran informasi saat ini",
              "image" => "s4.jpg",
              "tag" => "#halo #test"
            ],[
              "type" => "SK",
                "slug" => "slug-example3",
              "headline" => "Revamp Siapin LP Ma'arif",
              "tgl_upload" => Date::now(),
              "content" => "peluncuran informasi saat ini",
              "image" => "s4.jpg",
              "tag" => "#halo #test"
            ],[
              "type" => "SK",
                "slug" => "slug-example4",
              "headline" => "Revamp Siapin LP Ma'arif",
              "tgl_upload" => Date::now(),
              "content" => "peluncuran informasi saat ini",
              "image" => "s4.jpg",
              "tag" => "#halo #test"
            ],[
              "type" => "SK",
                "slug" => "slug-example5",
              "headline" => "Revamp Siapin LP Ma'arif",
              "tgl_upload" => Date::now(),
              "content" => "peluncuran informasi saat ini",
              "image" => "s4.jpg",
              "tag" => "#halo #test"
            ],[
              "type" => "SK",
                "slug" => "slug-example6",
              "headline" => "Revamp Siapin LP Ma'arif",
              "tgl_upload" => Date::now(),
              "content" => "peluncuran informasi saat ini",
              "image" => "s4.jpg",
              "tag" => "#halo #test"
            ],

        ];

        foreach ($jenjang as $row) {
            InformasiModel::create($row);
        }
    }
}
