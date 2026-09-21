<?php

namespace App\Exports;

use App\Models\Provinsi;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Wilayah implements FromCollection, WithMapping, WithHeadings, WithColumnWidths, WithStyles
{

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        return Provinsi::with("profile")
                ->whereHas("profile", function ($query) {
                    $query->where('id', '!=', null);
                })->get();
    }

    public function map($row): array
    {
        return [
            $row->kode_prov,
            'Wilayah '. $row->nm_prov,
            $row->nm_prov,
            $row->profile->kabupaten,
            $row->profile->kecamatan,
            $row->profile->kelurahan,
            $row->profile->alamat,
            $row->profile->lintang,
            $row->profile->bujur,
            $row->profile->website,
            $row->profile->ketua,
            $row->profile->telp_ketua,
            $row->profile->wakil_ketua,
            $row->profile->telp_wakil,
            $row->profile->bendahara,
            $row->profile->telp_bendahara,
            $row->profile->sekretaris,
            $row->profile->telp_sekretaris,
            $row->profile->masa_khidmat,
        ];
    }

    public function headings(): array
    {
        return [
            'Kode Wilayah',
            'Nama Wilayah',
            'Provinsi',
            'Kabupaten',
            'Kecamatan',
            'Kelurahan',
            'Alamat',
            'Lintang',
            'Bujur',
            'Website',
            'Ketua',
            'Telp. Ketua',
            'Wakil Ketua',
            'Telp. Wakil',
            'Bendahara',
            'Telp. Bendahara',
            'Sekretaris',
            'Telp. Sekretaris',
            'Masa Khidmat',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'B' => 25,
            'C' => 15,
            'D' => 15,
            'E' => 15,
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true],
                'fill' => ['fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID, 'startColor' => ['rgb' => 'FFA500']],
            ],
        ];
    }
}
