<?php
namespace App\Exports;
use App\Models\SuratTanah;
use Illuminate\Database\Eloquent\Builder;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Concerns\WithStyles;
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class SuratTanahExport implements FromCollection, WithHeadings, WithTitle, WithStyles
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
            : SuratTanah::orderBy('created_at', 'desc')->get();

        return $records->map(function ($item, $i) {
            return [
                'No'               => $i + 1,
                'Nama Dokumen'     => $item->nama_dokumen ?? $item->nama_sertifikat,
                'Nomor Sertifikat' => $item->nomor_sertifikat,
                'Jenis'            => $item->jenis_sertifikat,
                'Lokasi'           => $item->lokasi,
                'Luas'             => $item->luas,
                'Atas Nama'        => $item->atas_nama,
                'Status'           => $item->status_handover ?? 'Tersedia',
                'Keterangan'       => $item->keterangan,
                'Tgl Input'        => $item->tgl_input,
            ];
        });
    }

    public function headings(): array
    {
        return ['No', 'Nama Dokumen', 'Nomor Sertifikat', 'Jenis', 'Lokasi', 'Luas', 'Atas Nama', 'Status', 'Keterangan', 'Tgl Input'];
    }

    public function title(): string { return 'Arsip Surat Tanah'; }

    public function styles(Worksheet $sheet)
    {
        return [
            1 => ['font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF']], 'fill' => ['fillType' => 'solid', 'startColor' => ['rgb' => '4472C4']]],
        ];
    }
}