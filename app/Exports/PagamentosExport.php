<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{FromCollection, WithColumnWidths, WithEvents, WithHeadings, WithStyles, WithTitle};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\{Alignment, Border, Fill};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class PagamentosExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithEvents, WithColumnWidths
{
    protected Collection $pagamentos;

    public function __construct(Collection $pagamentos)
    {
        $this->pagamentos = $pagamentos;
    }

    public function collection(): Collection
    {
        return $this->pagamentos->map(fn($p) => [
            $p->cliente?->nome ?? '—',
            $p->tipo,
            number_format((float) $p->valor, 2, ',', '.'),
            $p->status,
            $p->forma_pagamento ?? '',
            $p->referencia_mes ?? '',
            $p->data_vencimento ? $p->data_vencimento->format('d/m/Y') : '',
            $p->data_pagamento ? $p->data_pagamento->format('d/m/Y') : '',
            $p->reserva?->area?->nome ?? '',
            $p->obs ?? '',
            $p->created_at->format('d/m/Y H:i'),
        ]);
    }

    public function headings(): array
    {
        return [
            'Cliente', 'Tipo', 'Valor', 'Status', 'Forma Pgto',
            'Mês Ref.', 'Vencimento', 'Pagamento', 'Área', 'Obs', 'Criado em',
        ];
    }

    public function title(): string
    {
        return 'Pagamentos';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 26, 'B' => 14, 'C' => 12, 'D' => 12, 'E' => 14,
            'F' => 10, 'G' => 13, 'H' => 13, 'I' => 18, 'J' => 35, 'K' => 16,
        ];
    }

    public function styles(Worksheet $sheet): array
    {
        return [
            1 => [
                'font' => ['bold' => true, 'color' => ['rgb' => 'FFFFFF'], 'size' => 10],
                'fill' => ['fillType' => Fill::FILL_SOLID, 'startColor' => ['rgb' => '1E293B']],
                'alignment' => ['horizontal' => Alignment::HORIZONTAL_CENTER, 'vertical' => Alignment::VERTICAL_CENTER],
            ],
        ];
    }

    public function registerEvents(): array
    {
        return [
            AfterSheet::class => function (AfterSheet $event) {
                $sheet = $event->sheet->getDelegate();
                $lastRow = $sheet->getHighestRow();
                $lastCol = 'K';

                $sheet->getRowDimension(1)->setRowHeight(28);
                if ($lastRow < 2) return;

                $sheet->getStyle("A2:{$lastCol}{$lastRow}")->applyFromArray([
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                ]);

                for ($row = 2; $row <= $lastRow; $row++) {
                    $status = $sheet->getCell("D{$row}")->getValue();
                    $color = match ($status) {
                        'PAGO'      => 'DCFCE7',
                        'PENDENTE'  => 'FEF3C7',
                        'ATRASADO'  => 'FEE2E2',
                        'CANCELADO' => 'F1F5F9',
                        default     => null,
                    };
                    if ($color) {
                        $sheet->getStyle("D{$row}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
                    }

                    $tipo = $sheet->getCell("B{$row}")->getValue();
                    $tipoColor = match ($tipo) {
                        'PAGAMENTO' => 'DCFCE7',
                        'CREDITO'   => 'DBEAFE',
                        'DEBITO'    => 'FEE2E2',
                        default     => null,
                    };
                    if ($tipoColor) {
                        $sheet->getStyle("B{$row}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($tipoColor);
                    }
                }

                $sheet->freezePane('A2');
                $sheet->setAutoFilter("A1:{$lastCol}1");
            },
        ];
    }
}