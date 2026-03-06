<?php

namespace App\Exports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\{FromCollection, WithColumnWidths, WithEvents, WithHeadings, WithStyles, WithTitle};
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Style\{Alignment, Border, Fill};
use PhpOffice\PhpSpreadsheet\Worksheet\Worksheet;

class ReservasExport implements FromCollection, WithHeadings, WithStyles, WithTitle, WithEvents, WithColumnWidths
{
    private const DIAS_PT = [
        'DOMINGO' => 'Domingo',
        'SEGUNDA' => 'Segunda',
        'TERCA'   => 'Terça',
        'QUARTA'  => 'Quarta',
        'QUINTA'  => 'Quinta',
        'SEXTA'   => 'Sexta',
        'SABADO'  => 'Sábado',
    ];

    protected Collection $reservas;

    public function __construct(Collection $reservas)
    {
        $this->reservas = $reservas;
    }

    public function collection(): Collection
    {
        return $this->reservas->map(fn($r) => [
            $r->area?->nome ?? '—',
            self::DIAS_PT[$r->dia_semana] ?? $r->dia_semana,
            $r->horario_inicio ? substr($r->horario_inicio instanceof \DateTimeInterface ? $r->horario_inicio->format('H:i') : $r->horario_inicio, 0, 5) : 'Dia inteiro',
            $r->horario_fim ? substr($r->horario_fim instanceof \DateTimeInterface ? $r->horario_fim->format('H:i') : $r->horario_fim, 0, 5) : '',
            $r->cliente?->nome ?? '—',
            $r->cliente?->telefone ?? '',
            $r->tipo,
            $r->data_reserva ? $r->data_reserva->format('d/m/Y') : '',
            $r->data_inicio ? $r->data_inicio->format('d/m/Y') : ($r->tipo !== 'UNICA' ? 'Indeterminada' : ''),
            $r->data_fim ? $r->data_fim->format('d/m/Y') : ($r->tipo !== 'UNICA' ? 'Indeterminada' : ''),
            $r->slots_ocupados ?? 1,
            $r->duracao_real_min ? $this->formatarDuracao($r->duracao_real_min) : '',
            $r->valor_unitario ? number_format((float) $r->valor_unitario, 2, ',', '.') : '',
            $r->valor_final ? number_format((float) $r->valor_final, 2, ',', '.') : '',
            $r->num_pessoas ?? '',
            $this->limparObs($r->obs),
        ]);
    }

    public function headings(): array
    {
        return [
            'Área',
            'Dia',
            'Início',
            'Fim',
            'Cliente',
            'Telefone',
            'Tipo',
            'Data Reserva',
            'Início Vigência',
            'Fim Vigência',
            'Slots',
            'Duração Real',
            'Valor Unit.',
            'Valor Final',
            'Pessoas',
            'Obs',
        ];
    }

    public function title(): string
    {
        return 'Reservas';
    }

    public function columnWidths(): array
    {
        return [
            'A' => 18,
            'B' => 11,
            'C' => 9,
            'D' => 9,
            'E' => 26,
            'F' => 15,
            'G' => 12,
            'H' => 13,
            'I' => 15,
            'J' => 15,
            'K' => 8,
            'L' => 13,
            'M' => 12,
            'N' => 12,
            'O' => 10,
            'P' => 40,
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
                $lastCol = 'P';

                $sheet->getRowDimension(1)->setRowHeight(28);
                if ($lastRow < 2) return;

                $sheet->getStyle("A2:{$lastCol}{$lastRow}")->applyFromArray([
                    'alignment' => ['vertical' => Alignment::VERTICAL_CENTER, 'wrapText' => true],
                    'borders' => ['allBorders' => ['borderStyle' => Border::BORDER_THIN, 'color' => ['rgb' => 'CBD5E1']]],
                ]);

                for ($row = 2; $row <= $lastRow; $row++) {
                    $tipo = $sheet->getCell("G{$row}")->getValue();
                    $color = match ($tipo) {
                        'FIXA'       => 'DBEAFE',
                        'MENSALISTA' => 'E0E7FF',
                        'UNICA'      => 'FEF3C7',
                        default      => null,
                    };
                    if ($color) {
                        $sheet->getStyle("G{$row}")->getFill()
                            ->setFillType(Fill::FILL_SOLID)->getStartColor()->setRGB($color);
                    }
                }

                $sheet->freezePane('A2');
                $sheet->setAutoFilter("A1:{$lastCol}1");
            },
        ];
    }

    private function formatarDuracao(int $minutos): string
    {
        $h = intdiv($minutos, 60);
        $m = $minutos % 60;
        return $m > 0 ? "{$h}h{$m}min" : "{$h}h";
    }

    private function limparObs(?string $obs): string
    {
        if (!$obs) return '';
        return str_replace(["\r\n", "\r"], "\n", trim($obs));
    }
}
