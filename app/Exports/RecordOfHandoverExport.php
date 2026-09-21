<?php

namespace App\Exports;

use App\Models\RecordOfHandover;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithMapping;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class RecordOfHandoverExport implements FromCollection, WithMapping, WithHeadings, ShouldAutoSize, WithStyles
{
    protected $query;
    protected $rowNumber = 0;

    public function __construct($query = null)
    {
        $this->query = $query;
    }

    public function collection()
    {
        if ($this->query) {
            return $this->query->with('user')->get();
        }
        return RecordOfHandover::with('user')->orderBy('created_at', 'desc')->get();
    }

    public function map($row): array
    {
        $this->rowNumber++;
        return [
            $this->rowNumber,
            $row->kategori ?? '-',
            $row->nama_dokumen ?? '-',
            $row->status ?? '-',
            $row->nama_peminjam ?? '-',
            $row->no_telp_peminjam ?? '-',
            $row->nama_bank ?? '-',
            $row->jangka_agunan ?? '-',
            $row->penanggung_agunan ?? '-',
            $row->no_telp_penanggung ?? '-',
            $row->nama_penerima ?? '-',
            $row->no_telp_penerima ?? '-',
            $row->nama_petugas ?? '-',
            $row->tgl_serahterima ? date('d/m/Y', strtotime($row->tgl_serahterima)) : '-',
            $row->catatan ?? '-',
        ];
    }

    public function headings(): array
    {
        return [
            'No',
            'Kategori',
            'Nama Dokumen / Aset',
            'Status',
            'Peminjam',
            'No. Telp Peminjam',
            'Bank Agunan',
            'Jangka Waktu Agunan',
            'Penanggung Jawab Agunan',
            'No. Telp Penanggung',
            'Penerima',
            'No. Telp Penerima',
            'Nama Petugas',
            'Tanggal Serah Terima',
            'Catatan',
        ];
    }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']],
                'fill' => [
                    'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                    'startColor' => ['rgb' => '00713D'],
                ],
            ],
        ];
    }
}
