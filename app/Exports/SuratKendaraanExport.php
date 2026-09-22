<?php

namespace App\Exports;

use App\Models\SuratKendaraan;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SuratKendaraanExport implements FromCollection, WithHeadings, WithTitle, WithStyles
{
    protected $query;

    public function __construct($query = null)
    {
        $this->query = $query;
    }

    public function collection()
    {
        $records = $this->query
            ? $this->query->get()
            : SuratKendaraan::orderBy('created_at', 'desc')->get();

        return $records->map(function ($item, $i) {
            return [
                'No'             => $i + 1,
                'Jenis Surat'    => $item->jenis_surat,
                'Nama Kendaraan' => $item->nama_kendaraan,
                'Nama Pemilik'   => $item->nama_pemilik ?? '-',
                'No Plat'        => $item->no_plat ?? '-',
                'No Rangka'      => $item->no_rangka ?? '-',
                'No Mesin'       => $item->no_mesin ?? '-',
                'Status'         => $item->status_handover ?? 'Tersedia',
                'Keterangan'     => $item->keterangan ?? '-',
                'Petugas'        => $item->petugas_name,
                'Tgl Input'      => $item->tgl_input ? $item->tgl_input->format('d/m/Y') : ($item->created_at ? $item->created_at->format('d/m/Y') : '-'),
            ];
        });
    }

    public function headings(): array
    {
        return [
            'No',
            'Jenis Surat',
            'Nama Kendaraan',
            'Nama Pemilik',
            'No Plat',
            'No Rangka',
            'No Mesin',
            'Status',
            'Keterangan',
            'Petugas',
            'Tgl Input'
        ];
    }

    public function title(): string
    {
        return 'Arsip Surat Kendaraan';
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '00713D']]
            ],
        ];
    }
}
