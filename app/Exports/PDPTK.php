<?php

namespace App\Exports;

use App\Models\PDPTK as ModelsPDPTK;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnWidths;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PDPTK implements FromCollection, WithMapping, WithHeadings, WithColumnWidths, WithStyles
{

    public function __construct($specificFilter, $lembaga, $tapel, $filter, $keyword, $filled)
    {
        $this->specificFilter = $specificFilter;
        $this->lembaga = $lembaga;
        $this->tapel = $tapel;
        $this->filter = $filter;
        $this->keywordFilter = $keyword;
        $this->filled = $filled;
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
        $filled = $this->filled;

        return ModelsPDPTK::with([
            'satpen:id_satpen,id_jenjang,id_prov,id_kab,no_registrasi,nm_satpen',
            'satpen.jenjang:id_jenjang,nm_jenjang',
            'satpen.provinsi:id_prov,nm_prov',
            'satpen.kabupaten:id_kab,nama_kab',
        ])
            ->where(function ($query) use ($specificFilter) {
                $query->whereHas('satpen', function ($q) use ($specificFilter) {
                    $q->where($specificFilter);
                });
            })
            ->where('tapel', '=', $this->tapel)
            ->whereIn('status_sinkron', $filled)
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
            $row->pd_lk,
            $row->pd_pr,
            $row->jml_pd,
            $row->guru_lk,
            $row->guru_pr,
            $row->jml_guru,
            $row->tendik_lk,
            $row->tendik_pr,
            $row->jml_tendik,
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
            'PD LK',
            'PD PR',
            'JML PD',
            'Guru LK',
            'Guru PR',
            'JML Guru',
            'Tendik LK',
            'Tendik PR',
            'JML Tendik',
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
