<?php

namespace App\Exports;

use App\Helpers\Date;
use App\Models\Others as ModelsOthers;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class Others implements FromCollection, WithMapping, WithHeadings, WithColumnWidths, WithStyles
{

    public function __construct($specificFilter, $lembaga, $filter, $keyword, $lingkungan_satpen, $akreditasi)
    {
        $this->specificFilter = $specificFilter;
        $this->lembaga = $lembaga;
        $this->filter = $filter;
        $this->keywordFilter = $keyword;
        $this->lingkungan_satpen = $lingkungan_satpen;
        $this->akreditasi = $akreditasi;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function collection()
    {
        $filter = $this->filter;
        $keywordFilter = $this->keywordFilter;
        $lembaga = $this->lembaga;
        $specificFilter = request()->specificFilter;
        $lingkungan_satpen = $this->lingkungan_satpen;
        $akreditasi = $this->akreditasi;

        return ModelsOthers::with([
            'satpen:id_satpen,id_jenjang,id_prov,id_kab,no_registrasi,nm_satpen',
            'satpen.jenjang:id_jenjang,nm_jenjang,lembaga',
            'satpen.provinsi:id_prov,nm_prov',
            'satpen.kabupaten:id_kab,nama_kab',
        ])
            ->where(function ($query) use ($specificFilter) {
                $query->whereHas('satpen', function ($q) use ($specificFilter) {
                    $q->where($specificFilter);
                });
            })
            ->whereIn('lingkungan_satpen', $lingkungan_satpen)
            ->whereIn('akreditasi', $akreditasi)
            ->whereHas('satpen', function ($query) use ($filter) {
                $query->where($filter);
            })
            ->whereHas('satpen.jenjang', function ($query) use ($lembaga) {
                $query->whereIn('lembaga', $lembaga);
            })
            ->whereHas('satpen', function ($query) use ($keywordFilter) {
                $query->where(function ($subQuery) use ($keywordFilter) {
                    foreach ($keywordFilter as $condition) {
                        $subQuery->orWhere(...$condition);
                    }
                });
            })
            ->get();
    }

    public function map($row): array
    {
        return [
            $row->satpen->no_registrasi,
            $row->satpen->nm_satpen,
            $row->satpen->jenjang->nm_jenjang,
            $row->satpen->provinsi->nm_prov,
            $row->satpen->kabupaten->nama_kab,
            $row->npyp,
            $row->naungan,
            $row->no_sk_pendirian,
            Date::tglReverseDash($row->tgl_sk_pendirian),
            $row->no_sk_operasional,
            Date::tglReverseDash($row->tgl_sk_operasional),
            $row->akreditasi,
            $row->website,
            $row->lingkungan_satpen,
            $row->last_sinkron,
        ];
    }

    public function headings(): array
    {
        return [
            'No. Registrasi',
            'Nama Satpen',
            'Jenjang',
            'Provinsi',
            'Kab/Kota',
            'NPYP',
            'Naungan',
            'No. SK Pendirian',
            'Tgl. SK Pendirian',
            'No. SK Operasional',
            'Tgl. SK Operasional',
            'Akreditasi',
            'Website',
            'Lingkungan Satpen',
            'Last Sync',
            'Last Sync',
        ];
    }

    public function columnWidths(): array
    {
        return [
            'B' => 25,
            'C' => 15,
            'D' => 20,
            'E' => 20,
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
