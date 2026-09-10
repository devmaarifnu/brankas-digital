<?php

namespace Database\Seeders;

use App\Models\Setting as SettingModel;
use Illuminate\Database\Seeder;

class Setting extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $settings = [
            [
                "describe" => 'JUMLAH SATPEN PERHALAMAN',
                "lookup" => 'count_perpage',
                "value" => 25,
            ],
            [
                "describe" => 'AWALAN PIAGAM',
                "lookup" => 'prefix_piagam_name',
                "value" => "Piagam Nomor Registrasi Ma'arif - ",
            ],
            [
                "describe" => 'AWALAN SK',
                "lookup" => 'prefix_sk_name',
                "value" => "SK Satuan Pendidikan BHPNU - ",
            ],
            [
                "describe" => 'TEMPLATE PIAGAM',
                "lookup" => 'template_piagam',
                "value" => "Piagam_Template.docx",
            ],
            [
                "describe" => 'TEMPLATE SK',
                "lookup" => 'template_sk',
                "value" => "SK_Template.docx",
            ],
            [
                "describe" => 'FORM OSS',
                "lookup" => 'oss_form',
                "value" => "https://docs.google.com/forms/d/e/1FAIpQLSd7KLQRHQN_LnFhty55aXFd1bZeCsOPzUt2xAfb1U9asIhPqg/viewform?usp=pp_url",
            ],
            [
                "describe" => 'SPREADSHEET OSS',
                "lookup" => 'oss_spreadsheet',
                "value" => "https://docs.google.com/spreadsheets/d/1WiOIiTCyJ3hOpA7C1g8LxpK9J_wDX78IGeTcM5vtNGE/edit?usp=sharing",
            ],


        ];

        foreach ($settings as $row) {
            SettingModel::create($row);
        }
    }
}
