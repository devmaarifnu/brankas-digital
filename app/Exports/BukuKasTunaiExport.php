<?php

namespace App\Exports;

use App\Models\KeuanganBukuKasTunai;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomStartCell;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\Alignment;
use PhpOffice\PhpSpreadsheet\Style\Border;
use PhpOffice\PhpSpreadsheet\Style\Fill;
use PhpOffice\PhpSpreadsheet\Worksheet\PageSetup;
use PhpOffice\PhpSpreadsheet\Worksheet\Table;
use PhpOffice\PhpSpreadsheet\Worksheet\Table\TableStyle;

class BukuKasTunaiExport implements FromCollection, WithHeadings, WithCustomStartCell, WithColumnFormatting, WithEvents, WithTitle
{
    public function __construct(private readonly KeuanganBukuKasTunai $report)
    {
    }

    public function collection(): Collection
    {
        $this->report->loadMissing('lampiranSaldoAwal');
        $transactions = $this->report->transaksi()->with('lampiranFiles')->orderBy('tanggal')->orderBy('id')->get();
        $rows = collect();
        $balance = (float) $this->report->saldo_awal;
        $number = 1;

        if ($balance > 0 || $this->report->saldo_awal_sumber_id) {
            $rows->push([
                $number++,
                Carbon::create($this->report->tahun, $this->report->bulan, 1)->format('d/m/Y'),
                '',
                'Sisa Saldo Bulan Sebelumnya',
                $balance,
                null,
                $balance,
                $this->report->lampiranSaldoAwal->pluck('file_path')->implode('; '),
            ]);
        }

        foreach ($transactions as $transaction) {
            $incoming = $transaction->jenis_transaksi === 'pemasukan' ? (float) $transaction->jumlah : null;
            $outgoing = $transaction->jenis_transaksi === 'pengeluaran' ? (float) $transaction->jumlah : null;
            $balance += ($incoming ?? 0) - ($outgoing ?? 0);

            $rows->push([
                $number++,
                $transaction->tanggal->format('d/m/Y'),
                $transaction->kode_akun,
                $transaction->transaksi,
                $incoming,
                $outgoing,
                $balance,
                $transaction->lampiranFiles->pluck('file_path')->implode('; '),
            ]);
        }

        if ($rows->isEmpty()) {
            $rows->push(['', '', '', 'Belum ada transaksi', null, null, null, '']);
        }

        return $rows;
    }

    public function headings(): array
    {
        return ['No', 'Tanggal', 'Kode Akun', 'Transaksi', 'Penerimaan', 'Pengeluaran', 'Saldo', 'Lampiran'];
    }

    public function startCell(): string
    {
        return 'A4';
    }

    public function title(): string
    {
        return 'Buku Kas Tunai';
    }

    public function columnFormats(): array
    {
        return [
            'E' => '"Rp" #,##0.00;[Red]-"Rp" #,##0.00',
            'F' => '"Rp" #,##0.00;[Red]-"Rp" #,##0.00',
            'G' => '"Rp" #,##0.00;[Red]-"Rp" #,##0.00',
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event): void {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();

                $sheet->mergeCells('A1:H1');
                $sheet->setCellValue('A1', 'BUKU KAS TUNAI');
                $sheet->mergeCells('A2:H2');
                $sheet->setCellValue('A2', sprintf(
                    'Periode: %02d/%04d  |  Sisa Saldo Bulan Sebelumnya: Rp %s',
                    $this->report->bulan,
                    $this->report->tahun,
                    number_format((float) $this->report->saldo_awal, 2, ',', '.')
                ));

                $sheet->getStyle('A1:H1')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 16, 'color' => ['rgb' => 'FFFFFF']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1F4E78']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getStyle('A2:H2')->applyFromArray([
                    'font' => ['bold' => true, 'size' => 11, 'color' => ['rgb' => '1F1F1F']],
                    'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => 'D9EAF7']],
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER],
                ]);
                $sheet->getRowDimension(1)->setRowHeight(28);
                $sheet->getRowDimension(2)->setRowHeight(22);

                $table = new Table('A4:H'.$lastRow, 'BukuKasTunaiRows');
                $tableStyle = new TableStyle();
                $tableStyle->setTheme(TableStyle::TABLE_STYLE_MEDIUM2);
                $tableStyle->setShowRowStripes(true);
                $table->setStyle($tableStyle);
                $sheet->addTable($table);

                $sheet->getStyle('A5:H'.$lastRow)->getAlignment()->setVertical(Alignment::VERTICAL_TOP);
                $sheet->getStyle('C5:D'.$lastRow)->getAlignment()->setWrapText(true);
                $sheet->getStyle('H5:H'.$lastRow)->getAlignment()->setWrapText(true);
                $sheet->getStyle('E5:G'.$lastRow)->getAlignment()->setHorizontal(Alignment::HORIZONTAL_RIGHT);
                $sheet->getStyle('A4:H'.$lastRow)->applyFromArray([
                    'borders' => ['insideHorizontal' => ['borderStyle' => Border::BORDER_HAIR, 'color' => ['rgb' => 'D9E2F3']]],
                ]);

                foreach (['A' => 8, 'B' => 14, 'C' => 48, 'D' => 38, 'E' => 20, 'F' => 20, 'G' => 20, 'H' => 42] as $column => $width) {
                    $sheet->getColumnDimension($column)->setWidth($width);
                }

                $sheet->freezePane('A5');
                $sheet->getPageSetup()->setOrientation(PageSetup::ORIENTATION_LANDSCAPE)->setFitToWidth(1)->setFitToHeight(0);
                $sheet->getPageSetup()->setPaperSize(PageSetup::PAPERSIZE_A4);
                $sheet->getPageMargins()->setTop(0.4)->setRight(0.3)->setLeft(0.3)->setBottom(0.4);
            },
        ];
    }
}
