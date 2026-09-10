<?php

namespace App\Exports;

use App\Helpers\Date;
use App\Models\Satpen;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SatpenExport implements FromCollection, WithMapping, WithHeadings, WithColumnWidths, WithStyles
{

    public function __construct($specificFilter, $statuses, $filter) {
        $this->specificFilter = $specificFilter;
        $this->statuses = $statuses;
        $this->filter = $filter;
    }
    /**
    * @return \Illuminate\Support\Collection
    */
    public function collection()
    {
        return Satpen::with([
            'kategori:id_kategori,nm_kategori',
            'provinsi:id_prov,nm_prov',
            'kabupaten:id_kab,nama_kab',
            'jenjang:id_jenjang,nm_jenjang',])
            ->whereIn('status', $this->statuses)
            ->where($this->specificFilter)
            ->where($this->filter)
            ->get();
    }

    public function map($row): array
    {
        return [
            $row->npsn,
            $row->no_registrasi,
            $row->nm_satpen,
            $row->jenjang->nm_jenjang,
            $row->kategori?->nm_kategori,
            $row->yayasan,
            $row->kepsek,
            $row->thn_berdiri,
            $row->email,
            $row->telpon,
            $row->fax,
            $row->provinsi->nm_prov,
            $row->kabupaten->nama_kab,
            $row->kecamatan,
            $row->kelurahan,
            $row->alamat,
            $row->aset_tanah,
            $row->nm_pemilik,
            Date::tglReverse($row->timeline[0]->tgl_status),
            $row->status,
        ];
    }

    public function headings(): array
    {
        return [
            'NPSN',
            'No. Registrasi',
            'Nama Satpen',
            'Jenjang Pendidikan',
            'Kategori',
            'Yayasan',
            'Kepala Sekolah',
            'Tahun Berdiri',
            'Email',
            'Telpon',
            'Fax',
            'Provinsi',
            'Kabupaten',
            'Kecamatan',
            'Keluarahan/Desa',
            'Alamat',
            'Aset Tanah',
            'Nama Pemilik Tanah',
            'Tanggal Registrasi',
            'Status Satpen',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'B' => 15,
            'C' => 25,
            'M' => 20,
            'N' => 20,
            'O' => 15,
            'P' => 35,
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
