<?php

namespace App\Exports;

use App\Models\PengurusCabang;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Cabang implements FromCollection, WithMapping, WithHeadings, WithColumnWidths, WithStyles
{

    public function __construct($specificFilter, $wilayah)
    {
        $this->specificFilter = $specificFilter;
        $this->wilayah = $wilayah;
    }

    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $specificFilter = request()->specificFilter;  
        if ($this->wilayah) {
            $wilayah = $this->wilayah;
            return PengurusCabang::with(["profile", "prov"])
                ->orderBy('id_pc', 'DESC')
                ->whereHas("profile", function ($query) {
                    $query->where('id', '!=', null);
                })
                ->whereHas("prov", function ($query) use ($wilayah) {
                    $query->where('id_prov', $wilayah);
                })
                ->get();
        } else {
            return PengurusCabang::with(["profile", "prov"])
                ->orderBy('id_pc', 'DESC')
                ->whereHas("profile", function ($query) {
                    $query->where('id', '!=', null);
                })
                ->whereHas("prov", function ($query) use ($specificFilter) {
                    $query->where($specificFilter);
                })
                ->get();
        }
    }

    public function map($row): array
    {
        return [
            $row->kode_kab,
            'Cabang ' . $row->nama_pc,
            $row->prov->nm_prov,
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
            'Kode Cabang',
            'Nama Cabang',
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
