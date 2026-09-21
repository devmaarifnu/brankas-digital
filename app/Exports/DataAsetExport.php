<?php
namespace App\Exports;
use App\Models\DataAsetLembaga;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class DataAsetExport implements FromCollection, WithHeadings, WithTitle, WithStyles
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
            : DataAsetLembaga::orderBy('created_at', 'desc')->get();

        return $records->map(function ($item, $i) {
            return [
                'No'         => $i + 1,
                'Nama Aset'  => $item->nama_dokumen ?? $item->nama_aset,
                'Jenis Aset' => $item->jenis_aset ?? $item->jenis_sertifikat,
                'Nilai Aset' => $item->nilai_aset,
                'Lokasi'     => $item->lokasi,
                'Status'     => $item->status_handover ?? 'Tersedia',
                'Keterangan' => $item->keterangan,
                'Tgl Input'  => $item->tgl_input,
            ];
        });
    }

    public function headings(): array
    {
        return ['No', 'Nama Aset', 'Jenis Aset', 'Nilai Aset', 'Lokasi', 'Status', 'Keterangan', 'Tgl Input'];
    }

    public function title(): string { return 'Data Aset Lembaga'; }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]],
        ];
    }
}