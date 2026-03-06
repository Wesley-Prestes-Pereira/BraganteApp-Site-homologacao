<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{FromCollection, WithColumnWidths, WithEvents, WithHeadings, WithStyles, WithTitle};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\{Alignment, Border, Fill};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class AuditsExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithEvents, WithColumnWidths
{
    protected Collection $audits;

    public function __construct(Collection $audits)
    {
        $this->audits = $audits;
    }

    public function collection(): Collection
    {
        return $this->audits->map(fn($a) => [
            $a->created_at->format('d/m/Y H:i'),
            $a->user_name,
            $a->event_label,
            $a->model_label,
            $a->description,
            collect($a->changes)->map(function ($c) {
                if ($c['type'] === 'added') return "{$c['field']}: {$c['new']}";
                if ($c['type'] === 'removed') return "{$c['field']}: {$c['old']}";
                return "{$c['field']}: {$c['old']} → {$c['new']}";
            })->implode("\n") ?: '—',
            $a->ip ?? '—',
        ]);
    }

    public function headings(): array
    {
        return ['Data/Hora', 'Usuário', 'Evento', 'Entidade', 'Descrição', 'Alterações', 'IP'];
    }

    public function title(): string
    {
        return 'Histórico';
    }

    public function columnWidths(): array
    {
        return ['A' => 18, 'B' => 20, 'C' => 14, 'D' => 16, 'E' => 45, 'F' => 55, 'G' => 18];
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

                $sheet->getRowDimension(1)->setRowHeight(28);
                if ($lastRow < 2) return;

                $sheet->getStyle("A2:G{$lastRow}")->applyFromArray([
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                ]);

                $sheet->getStyle("A1:G1")->applyFromArray([
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => '94A3B8']]],
                ]);

                for ($row = 2; $row <= $lastRow; $row++) {
                    $event = $sheet->getCell("C{$row}")->getValue();
                    $color = match ($event) {
                        'Criação'     => 'DCFCE7',
                        'Exclusão'    => 'FEE2E2',
                        'Atualização' => 'DBEAFE',
                        'Restauração' => 'FEF3C7',
                        default       => null,
                    };
                    if ($color) {
                        $sheet->getStyle("C{$row}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
                    }

                    $changes = $sheet->getCell("F{$row}")->getValue();
                    $lines = $changes ? substr_count((string) $changes, "\n") + 1 : 1;
                    $sheet->getRowDimension($row)->setRowHeight(max(22, min($lines * 14, 80)));
                }

                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:G1');
            },
        ];
    }
}
