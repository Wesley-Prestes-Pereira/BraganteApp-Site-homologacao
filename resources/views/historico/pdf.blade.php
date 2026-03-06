<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8">
    <title>Histórico de Atividades</title>
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

        table.lista {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 6px;
            table-layout: fixed;
        }

        table.lista col.col-data {
            width: 10%;
        }

        table.lista col.col-usuario {
            width: 10%;
        }

        table.lista col.col-evento {
            width: 8%;
        }

        table.lista col.col-entidade {
            width: 10%;
        }

        table.lista col.col-descricao {
            width: 26%;
        }

        table.lista col.col-alteracoes {
            width: 30%;
        }

        table.lista col.col-ip {
            width: 6%;
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

        table.lista tr.created td:first-child {
            border-left: 3px solid #22c55e;
        }

        table.lista tr.updated td:first-child {
            border-left: 3px solid #3b82f6;
        }

        table.lista tr.deleted td:first-child {
            border-left: 3px solid #ef4444;
        }

        table.lista tr.restored td:first-child {
            border-left: 3px solid #f59e0b;
        }

        .badge {
            display: inline-block;
            padding: 1px 4px;
            border-radius: 2px;
            font-size: 6.5px;
            font-weight: 700;
        }

        .badge-created {
            background: #dcfce7;
            color: #166534;
        }

        .badge-updated {
            background: #dbeafe;
            color: #1e40af;
        }

        .badge-deleted {
            background: #fee2e2;
            color: #991b1b;
        }

        .badge-restored {
            background: #fef3c7;
            color: #92400e;
        }

        .change-old {
            color: #991b1b;
            text-decoration: line-through;
        }

        .change-new {
            color: #166534;
        }

        .change-arrow {
            color: #94a3b8;
        }

        .footer {
            margin-top: 4px;
            font-size: 6.5px;
            color: #94a3b8;
            text-align: center;
        }
    </style>
</head>

<body>
    <div class="header">
        <table class="header-table">
            <tr>
                <td>
                    <h1>Histórico de Atividades</h1>
                    <div class="sub">Complexo Esportivo Bragante — Show de Bola</div>
                </td>
                <td class="info">
                    Gerado em {{ now()->format('d/m/Y H:i') }}<br>
                    Total: {{ $audits->count() }} registros
                </td>
            </tr>
        </table>
    </div>

    <div class="stats">
        <span>Criações: <strong>{{ $totalCriados }}</strong></span>
        <span>Edições: <strong>{{ $totalEditados }}</strong></span>
        <span>Exclusões: <strong>{{ $totalExcluidos }}</strong></span>
        <span>Restaurações: <strong>{{ $totalRestaurados }}</strong></span>
    </div>

    <table class="lista">
        <colgroup>
            <col class="col-data">
            <col class="col-usuario">
            <col class="col-evento">
            <col class="col-entidade">
            <col class="col-descricao">
            <col class="col-alteracoes">
            <col class="col-ip">
        </colgroup>
        <thead>
            <tr>
                <th>Data/Hora</th>
                <th>Usuário</th>
                <th>Evento</th>
                <th>Entidade</th>
                <th>Descrição</th>
                <th>Alterações</th>
                <th>IP</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($audits as $audit)
                <tr class="{{ $audit->event }}">
                    <td>{{ $audit->created_at->format('d/m/Y H:i') }}</td>
                    <td>{{ $audit->user_name }}</td>
                    <td><span class="badge badge-{{ $audit->event }}">{{ $audit->event_label }}</span></td>
                    <td>{{ $audit->model_label }}</td>
                    <td>{{ $audit->description }}</td>
                    <td>
                        @if (empty($audit->changes))
                            —
                        @else
                            @foreach ($audit->changes as $change)
                                <strong>{{ $change['field'] }}:</strong>
                                @if ($change['type'] === 'changed')
                                    <span class="change-old">{{ $change['old'] }}</span>
                                    <span class="change-arrow">→</span>
                                    <span class="change-new">{{ $change['new'] }}</span>
                                @elseif ($change['type'] === 'added')
                                    <span class="change-new">{{ $change['new'] }}</span>
                                @elseif ($change['type'] === 'removed')
                                    <span class="change-old">{{ $change['old'] }}</span>
                                @endif
                                @if (!$loop->last)
                                    <br>
                                @endif
                            @endforeach
                        @endif
                    </td>
                    <td>{{ $audit->ip ?? '—' }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        Show de Bola — Complexo Esportivo Bragante &bull; Gerado automaticamente em
        {{ now()->format('d/m/Y \à\s H:i') }}
    </div>
</body>

</html>