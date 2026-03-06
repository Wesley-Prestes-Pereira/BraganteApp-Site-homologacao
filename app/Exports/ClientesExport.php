<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{FromCollection, WithColumnWidths, WithEvents, WithHeadings, WithStyles, WithTitle};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\{Alignment, Border, Fill};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ClientesExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithEvents, WithColumnWidths
{
    protected Collection $clientes;

    public function __construct(Collection $clientes)
    {
        $this->clientes = $clientes;
    }

    public function collection(): Collection
    {
        return $this->clientes->map(fn($c) => [
            $c->nome,
            $c->telefone ?? '',
            $c->email ?? '',
            $c->cpf ?? '',
            $c->ativo ? 'Sim' : 'Não',
            $c->reservas_count ?? 0,
            $c->obs ?? '',
        ]);
    }

    public function headings(): array
    {
        return ['Nome', 'Telefone', 'Email', 'CPF', 'Ativo', 'Reservas', 'Obs'];
    }

    public function title(): string
    {
        return 'Clientes';
    }

    public function columnWidths(): array
    {
        return ['A' => 30, 'B' => 16, 'C' => 28, 'D' => 16, 'E' => 8, 'F' => 10, 'G' => 40];
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

                $sheet->freezePane('A2');
                $sheet->setAutoFilter('A1:G1');
            },
        ];
    }
}