<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
//       $this->call(User::class);
//       $this->call(Jenjang::class);
//       $this->call(Provinsi::class);
//       $this->call(Kategori::class);
//       $this->call(Kabupaten::class);
//       $this->call(PengurusCabang::class);
//       $this->call(Informasi::class);
        // $this->call(Satpen::class);
       $this->call(Setting::class);
    }
}
