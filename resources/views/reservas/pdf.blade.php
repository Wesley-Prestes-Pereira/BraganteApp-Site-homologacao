<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>{{ $titulo }} - Reservas</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm 10mm;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: DejaVu Sans, Arial, Helvetica, sans-serif;
            font-size: 7.5px;
            color: #111;
            background: #fff;
        }

        .header {
            width: 100%;
            margin-bottom: 6px;
            padding-bottom: 5px;
            border-bottom: 2px solid #1e293b;
        }

        .header-table {
            width: 100%;
            border: none;
        }

        .header-table td {
            border: none;
            padding: 0;
            vertical-align: bottom;
        }

        .header h1 {
            font-size: 14px;
            color: #1e293b;
            margin: 0;
        }

        .header .sub {
            font-size: 8.5px;
            color: #475569;
            margin-top: 2px;
        }

        .header .info {
            text-align: right;
            font-size: 7.5px;
            color: #64748b;
            line-height: 1.4;
        }

        .stats {
            margin-bottom: 5px;
            font-size: 7.5px;
            color: #475569;
        }

        .stats span {
            margin-right: 12px;
        }

        .stats strong {
            color: #1e293b;
        }

        .legend {
            margin-bottom: 5px;
            font-size: 7px;
            color: #64748b;
        }

        .legend-dot {
            display: inline-block;
            width: 8px;
            height: 3px;
            border-radius: 1px;
            vertical-align: middle;
            margin-right: 2px;
        }

        .legend-dot--fixa {
            background: #3b82f6;
        }

        .legend-dot--unica {
            background: #f59e0b;
        }

        .legend-dot--mensalista {
            background: #8b5cf6;
        }

        .legend-item {
            margin-right: 10px;
        }

        table.lista {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            table-layout: fixed;
        }

        table.lista col.col-area {
            width: 10%;
        }

        table.lista col.col-dia {
            width: 6%;
        }

        table.lista col.col-data {
            width: 8%;
        }

        table.lista col.col-horario {
            width: 8%;
        }

        table.lista col.col-cliente {
            width: 14%;
        }

        table.lista col.col-telefone {
            width: 8%;
        }

        table.lista col.col-tipo {
            width: 6%;
        }

        table.lista col.col-valor {
            width: 7%;
        }

        table.lista col.col-vigencia {
            width: 12%;
        }

        table.lista col.col-obs {
            width: 21%;
        }

        table.lista th {
            background: #1e293b;
            color: #fff;
            padding: 4px 5px;
            font-size: 7px;
            font-weight: 700;
            text-transform: uppercase;
            text-align: left;
            letter-spacing: .3px;
        }

        table.lista th:first-child {
            border-radius: 3px 0 0 0;
        }

        table.lista th:last-child {
            border-radius: 0 3px 0 0;
        }

        table.lista td {
            border-bottom: 1px solid #e2e8f0;
            padding: 3px 5px;
            font-size: 7px;
            vertical-align: top;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        table.lista tr:nth-child(even) td {
            background: #f8fafc;
        }

        table.lista tr.fixa td:first-child {
            border-left: 3px solid #3b82f6;
        }

        table.lista tr.unica td:first-child {
            border-left: 3px solid #f59e0b;
        }

        table.lista tr.mensalista td:first-child {
            border-left: 3px solid #8b5cf6;
        }

        .badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 3px;
            font-size: 6px;
            font-weight: 700;
            letter-spacing: .3px;
        }

        .badge-fixa {
            background: #dbeafe;
            color: #1d4ed8;
        }

        .badge-unica {
            background: #fef3c7;
            color: #92400e;
        }

        .badge-mensalista {
            background: #ede9fe;
            color: #5b21b6;
        }

        .obs-text {
            font-style: italic;
            color: #475569;
            word-wrap: break-word;
            overflow-wrap: break-word;
            white-space: pre-line;
            font-size: 6.5px;
            line-height: 1.3;
        }

        .tel {
            color: #475569;
            white-space: nowrap;
        }

        .vigencia {
            font-size: 6.5px;
            color: #64748b;
            white-space: nowrap;
        }

        .data-col {
            white-space: nowrap;
        }

        .cliente-nome {
            font-weight: 700;
            word-wrap: break-word;
            overflow-wrap: break-word;
        }

        .horario-val {
            font-weight: 700;
            white-space: nowrap;
        }

        .valor-col {
            white-space: nowrap;
            text-align: right;
        }

        .footer {
            margin-top: 4px;
            text-align: center;
            font-size: 6.5px;
            color: #94a3b8;
        }
    </style>
</head>

<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <h1>{{ $titulo }}</h1>
                    <div class="sub">{{ $descricao }}</div>
                </td>
                <td class="info">
                    Show de Bola &mdash; Bragante<br>
                    {{ now()->format('d/m/Y H:i') }}
                </td>
            </tr>
        </table>
    </div>

    <div class="stats">
        <span><strong>{{ $reservas->count() }}</strong> reservas</span>
        <span><strong>{{ $totalFixas }}</strong> fixas</span>
        <span><strong>{{ $totalUnicas }}</strong> únicas</span>
        <span><strong>{{ $totalMensalistas }}</strong> mensalistas</span>
        <span><strong>{{ $totalAreas }}</strong> áreas</span>
    </div>

    <div class="legend">
        <span class="legend-item"><span class="legend-dot legend-dot--fixa"></span> Fixa</span>
        <span class="legend-item"><span class="legend-dot legend-dot--unica"></span> Única</span>
        <span class="legend-item"><span class="legend-dot legend-dot--mensalista"></span> Mensalista</span>
    </div>

    <table class="lista">
        <colgroup>
            <col class="col-area">
            <col class="col-dia">
            <col class="col-data">
            <col class="col-horario">
            <col class="col-cliente">
            <col class="col-telefone">
            <col class="col-tipo">
            <col class="col-valor">
            <col class="col-vigencia">
            <col class="col-obs">
        </colgroup>
        <thead>
            <tr>
                <th>Área</th>
                <th>Dia</th>
                <th>Data</th>
                <th>Horário</th>
                <th>Cliente</th>
                <th>Telefone</th>
                <th>Tipo</th>
                <th>Valor</th>
                <th>Vigência</th>
                <th>Obs</th>
            </tr>
        </thead>
        <tbody>
            @forelse($reservas as $r)
                <tr class="{{ strtolower($r->tipo) }}">
                    <td>{{ $r->area->nome ?? '—' }}</td>
                    <td>{{ $diasPt[$r->dia_semana] ?? $r->dia_semana }}</td>
                    <td class="data-col">
                        @if ($r->tipo === 'UNICA' && $r->data_reserva)
                            {{ $r->data_reserva->format('d/m/Y') }}
                        @elseif(isset($diasDatas[$r->dia_semana]))
                            {{ $diasDatas[$r->dia_semana] }}
                        @else
                            —
                        @endif
                    </td>
                    <td>
                        @if ($r->horario_inicio)
                            <span
                                class="horario-val">{{ substr($r->horario_inicio instanceof \DateTimeInterface ? $r->horario_inicio->format('H:i') : $r->horario_inicio, 0, 5) }}</span>
                            @if ($r->horario_fim && $r->slots_ocupados > 1)
                                <br>{{ substr($r->horario_fim instanceof \DateTimeInterface ? $r->horario_fim->format('H:i') : $r->horario_fim, 0, 5) }}
                            @endif
                        @else
                            <span class="horario-val">Dia inteiro</span>
                        @endif
                    </td>
                    <td><span class="cliente-nome">{{ $r->cliente->nome ?? '—' }}</span></td>
                    <td class="tel">{{ $r->cliente->telefone ?? '—' }}</td>
                    <td><span class="badge badge-{{ strtolower($r->tipo) }}">{{ $r->tipo }}</span></td>
                    <td class="valor-col">
                        @if ($r->valor_final)
                            R$ {{ number_format((float) $r->valor_final, 2, ',', '.') }}
                        @else
                            —
                        @endif
                    </td>
                    <td class="data-col">
                        @if (in_array($r->tipo, ['FIXA', 'MENSALISTA']))
                            <span class="vigencia">
                                @if ($r->data_inicio && $r->data_fim)
                                    {{ $r->data_inicio->format('d/m/Y') }} a {{ $r->data_fim->format('d/m/Y') }}
                                @elseif ($r->data_inicio)
                                    A partir de {{ $r->data_inicio->format('d/m/Y') }}
                                @elseif ($r->data_fim)
                                    Até {{ $r->data_fim->format('d/m/Y') }}
                                @else
                                    Indeterminada
                                @endif
                            </span>
                        @else
                            —
                        @endif
                    </td>
                    <td><span class="obs-text">{{ $r->obs ?? '' }}</span></td>
                </tr>
            @empty
                <tr>
                    <td colspan="10" style="text-align:center;padding:20px;color:#94a3b8;">Nenhuma reserva encontrada
                    </td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">
        Show de Bola Bragante &mdash; {{ date('Y') }}
    </div>
</body>

</html>
