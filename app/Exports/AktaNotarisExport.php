<?php
namespace App\Exports;
use App\Models\AktaNotaris;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AktaNotarisExport implements FromCollection, WithHeadings, WithTitle, WithStyles
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
            : AktaNotaris::orderBy('created_at', 'desc')->get();

        return $records->map(function ($item, $i) {
            return [
                'No'            => $i + 1,
                'Nama Dokumen'  => $item->nama_dokumen ?? $item->nama_sertifikat,
                'Nomor Akta'    => $item->nomor_akta ?? $item->nomor_sertifikat,
                'Jenis Dokumen' => $item->jenis_dokumen,
                'Nama Notaris'  => $item->nama_notaris,
                'Tanggal Akta'  => $item->tanggal_akta,
                'Status'        => $item->status_handover ?? 'Tersedia',
                'Keterangan'    => $item->keterangan,
                'Tgl Input'     => $item->tgl_input,
            ];
        });
    }

    public function headings(): array
    {
        return ['No', 'Nama Dokumen', 'Nomor Akta', 'Jenis Dokumen', 'Nama Notaris', 'Tanggal Akta', 'Status', 'Keterangan', 'Tgl Input'];
    }

    public function title(): string { return 'Akta Notaris'; }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]],
        ];
    }
}