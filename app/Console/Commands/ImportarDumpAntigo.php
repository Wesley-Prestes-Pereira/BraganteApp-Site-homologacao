<?php

namespace App\Console\Commands;

use App\Models\{Area, Cliente, Pagamento, Reserva};
use Carbon\Carbon;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\{Cache, DB};
use Illuminate\Support\Str;

class ImportarDumpAntigo extends Command
{
    protected $signature = 'importar:dump-antigo {arquivo? : Caminho do arquivo SQL (padrão: storage/app/import/appbra79_app_2025.sql)}';
    protected $description = 'Importa dados do dump SQL do sistema antigo';

    private int $clientesCriados = 0;
    private int $reservasImportadas = 0;
    private int $pagamentosCriados = 0;
    private int $telefonesExtraidos = 0;
    private int $tiposCorrigidos = 0;
    private int $valoresExtraidos = 0;
    private int $erros = 0;
    private array $errosDetalhados = [];
    private array $clientesCache = [];

    private const ARQUIVO_PADRAO = 'storage/app/import/appbra79_app_2025.sql';

    private const MAPA_AREAS = [
        1  => 'Quadra 1',
        2  => 'Quadra 2',
        3  => 'Quadra 2',
        4  => 'Quadra Futsal',
        5  => 'Quadra Futsal',
        6  => 'Churrasqueira 1',
        7  => 'Churrasqueira 2',
        8  => 'Churrasqueira 3',
        9  => 'Churrasqueira 4',
        10 => 'Churrasqueira 5',
        11 => 'Churrasqueira 6',
        12 => 'Churrasqueira 7',
    ];

    private const PADROES_MENSALISTA = [
        '/\bmensal(ista)?\b/i',
        '/\bmensal\s*[=:]\s*/i',
        '/\bMENSL?ALISTA[S]?\b/i',
    ];

    private const PADROES_FIXO = [
        '/\b(horario|horário)\s*(fixo)\b/i',
        '/\(fixo\)/i',
        '/\bfixo\b(?!\s*=)/i',
    ];

    public function handle(): int
    {
        $arquivo = $this->argument('arquivo') ?? base_path(self::ARQUIVO_PADRAO);

        if (!file_exists($arquivo)) {
            $this->error("Arquivo não encontrado: {$arquivo}");
            $this->line('  Coloque o dump em: ' . base_path(self::ARQUIVO_PADRAO));
            return 1;
        }

        $this->info('═══════════════════════════════════════════════');
        $this->info(' IMPORTAÇÃO DO DUMP ANTIGO - SDB');
        $this->info('═══════════════════════════════════════════════');
        $this->info(" Arquivo: {$arquivo}");
        $this->newLine();

        $conteudo = file_get_contents($arquivo);

        $this->info('▸ Parseando reservas do dump...');
        $reservasRaw = $this->parsearInserts($conteudo);
        $total = count($reservasRaw);
        $this->info("  Encontradas: {$total} reservas");

        if ($total === 0) {
            $this->warn('Nenhuma reserva encontrada no dump.');
            return 0;
        }

        $this->info('▸ Mapeando áreas...');
        $areasMap = $this->mapearAreas();
        $this->info('  Áreas mapeadas: ' . count($areasMap));

        $this->info('▸ Processando reservas...');
        $this->newLine();

        $bar = $this->output->createProgressBar($total);
        $bar->start();

        DB::beginTransaction();

        try {
            foreach ($reservasRaw as $raw) {
                $this->processarReserva($raw, $areasMap);
                $bar->advance();
            }

            DB::commit();
            $bar->finish();
        } catch (\Exception $e) {
            DB::rollBack();
            $bar->finish();
            $this->newLine(2);
            $this->error("Erro fatal: {$e->getMessage()}");
            $this->error($e->getTraceAsString());
            return 1;
        }

        $this->newLine(2);
        $this->limparCaches();
        $this->exibirRelatorio();

        return 0;
    }

    private function parsearInserts(string $sql): array
    {
        $reservas = [];

        $sql = str_replace("\r\n", "\n", $sql);

        if (!preg_match_all(
            '/\((\d+),\s*(\d+),\s*\'([A-Z]+)\',\s*\'([^\']+)\',\s*\'((?:[^\'\\\\]|\\\\.)*)\',\s*(NULL|\'(?:[^\'\\\\]|\\\\.)*\'),\s*\'([A-Z]+)\',\s*\'([^\']+)\',\s*(NULL|\'(?:[^\'\\\\]|\\\\.)*\'),\s*(NULL|\'[^\']*\')\)/s',
            $sql,
            $matches,
            PREG_SET_ORDER
        )) {
            return $reservas;
        }

        foreach ($matches as $m) {
            $obs = $m[9] === 'NULL' ? null : trim($m[9], "'");
            $obs = $obs ? str_replace("\\n", "\n", $obs) : null;

            $reservas[] = [
                'id'           => (int) $m[1],
                'recursoId'    => (int) $m[2],
                'diaSemana'    => $m[3],
                'horario'      => $m[4],
                'cliente'      => str_replace("\\'", "'", $m[5]),
                'telefone'     => $m[6] === 'NULL' ? null : trim(str_replace("\\'", "'", $m[6]), "'"),
                'tipo'         => $m[7],
                'createdAt'    => $m[8],
                'obs'          => $obs,
                'data_reserva' => $m[10] === 'NULL' ? null : trim($m[10], "'"),
            ];
        }

        return $reservas;
    }

    private function mapearAreas(): array
    {
        $mapa = [];

        foreach (self::MAPA_AREAS as $recursoId => $nomeArea) {
            $area = Area::where('nome', $nomeArea)->first();

            if ($area) {
                $mapa[$recursoId] = $area->id;
            } else {
                $this->warn("  Área não encontrada: {$nomeArea}");
            }
        }

        return $mapa;
    }

    private function processarReserva(array $raw, array $areasMap): void
    {
        try {
            $areaId = $areasMap[$raw['recursoId']] ?? null;

            if (!$areaId) {
                $this->registrarErro($raw['id'], "Área não mapeada para recursoId={$raw['recursoId']}");
                return;
            }

            [$clienteNome, $telefone] = $this->extrairClienteTelefone($raw['cliente'], $raw['telefone']);
            $tipo = $this->detectarTipo($raw['obs'], $clienteNome);
            $valor = $this->extrairValor($raw['obs']);
            $duracao = $this->extrairDuracao($raw['cliente'], $raw['obs']);
            [$horarioInicio, $horarioFim, $slots] = $this->calcularHorarios($raw['horario'], $duracao);
            $dataReserva = $this->parsearData($raw['data_reserva']);

            $area = Area::findCached($areaId);
            $isDiaInteiro = $area && $area->modo_reserva === 'DIA_INTEIRO';

            $cliente = $this->obterOuCriarCliente($clienteNome, $telefone);
            $obsLimpa = $this->limparObs($raw['obs']);

            $reserva = Reserva::create([
                'area_id'          => $areaId,
                'cliente_id'       => $cliente->id,
                'dia_semana'       => $raw['diaSemana'],
                'tipo'             => $tipo,
                'horario_inicio'   => $isDiaInteiro ? null : $horarioInicio,
                'horario_fim'      => $isDiaInteiro ? null : $horarioFim,
                'slots_ocupados'   => $isDiaInteiro ? 1 : $slots,
                'duracao_real_min' => $duracao,
                'data_reserva'     => $tipo === 'UNICA' ? $dataReserva : null,
                'data_inicio'      => in_array($tipo, ['FIXA', 'MENSALISTA']) ? ($dataReserva ?? Carbon::parse($raw['createdAt'])->toDateString()) : null,
                'data_fim'         => null,
                'valor_unitario'   => $valor,
                'valor_total'      => $valor ? ($valor * $slots) : null,
                'valor_final'      => $valor ? ($valor * $slots) : null,
                'obs'              => $obsLimpa,
                'obs_sistema'      => $this->gerarObsSistema($duracao, $slots, $raw['id']),
                'created_at'       => $raw['createdAt'],
                'updated_at'       => $raw['createdAt'],
            ]);

            $this->reservasImportadas++;

            $this->extrairPagamentos($raw['obs'], $cliente, $reserva, $raw['createdAt']);
        } catch (\Exception $e) {
            $this->registrarErro($raw['id'], $e->getMessage());
        }
    }

    private function extrairClienteTelefone(string $clienteRaw, ?string $telefoneRaw): array
    {
        $cliente = trim($clienteRaw);
        $telefone = $telefoneRaw ? trim($telefoneRaw) : null;

        $cliente = preg_replace('/\s*(INICIO|INÍCIO)\s+\d+[Hh]\d*\s*(AS|ÀS|A)\s*\d+[Hh]\d*/i', '', $cliente);
        $cliente = preg_replace('/\s+\d+[Hh]\d*\s+(às|as|AS|ÀS|A)\s+\d+[Hh]\d*\s*$/i', '', $cliente);
        $cliente = preg_replace('/\s*\d+\s*hora.*$/i', '', $cliente);
        $cliente = preg_replace('/\s*1\s*H\s*\d+.*$/i', '', $cliente);
        $cliente = preg_replace('/\s*mais\s+\d+\s*minutos.*$/i', '', $cliente);
        $cliente = preg_replace('/\s*alt\s+\d+[,\.]\d+\s*$/i', '', $cliente);

        if (!$telefone) {
            if (preg_match('/^(.+?)\s*[-–]\s*(\+?\d[\d\s\-().]{6,})(.*)$/', $cliente, $m)) {
                $nomeParte = trim($m[1]);
                $foneParte = preg_replace('/[^\d+]/', '', $m[2]);
                $restante = trim($m[3]);

                if (strlen($foneParte) >= 8) {
                    $telefone = $foneParte;
                    $cliente = $nomeParte;
                    $this->telefonesExtraidos++;

                    if ($restante && !preg_match('/^\d/', $restante)) {
                        $cliente .= ' ' . trim($restante, ' -–');
                    }
                }
            } elseif (preg_match('/^(.+?)\s*\((\d[\d\s\-().]{6,})\)(.*)$/', $cliente, $m)) {
                $telefone = preg_replace('/[^\d+]/', '', $m[2]);
                $cliente = trim($m[1]) . (trim($m[3]) ? ' ' . trim($m[3]) : '');
                $this->telefonesExtraidos++;
            } elseif (preg_match('/^(.+?)\s+(\d{4,5}[-\s]?\d{4})\s*$/', $cliente, $m)) {
                $foneParte = preg_replace('/[^\d]/', '', $m[2]);
                if (strlen($foneParte) >= 8) {
                    $telefone = $foneParte;
                    $cliente = trim($m[1]);
                    $this->telefonesExtraidos++;
                }
            }
        }

        $cliente = preg_replace('/\s*\(?\+?\s*55\s*\)?\s*/', ' ', $cliente);
        $cliente = preg_replace('/\s*(livre|LIVRE)\s+\d+\/\d+/i', '', $cliente);
        $cliente = preg_replace('/\s*matr?\.\s*\d+/i', '', $cliente);
        $cliente = preg_replace('/\s+/', ' ', trim($cliente));
        $cliente = rtrim($cliente, ' -–/');

        if ($telefone) {
            $telefone = preg_replace('/[^\d]/', '', $telefone);
            if (strlen($telefone) >= 12 && str_starts_with($telefone, '55')) {
                $telefone = substr($telefone, -9);
            } elseif (strlen($telefone) === 11 && str_starts_with($telefone, '41')) {
                $telefone = substr($telefone, 2);
            }

            if (strlen($telefone) === 9 && str_starts_with($telefone, '9')) {
                $telefone = substr($telefone, 0, 5) . '-' . substr($telefone, 5);
            } elseif (strlen($telefone) === 8) {
                $telefone = substr($telefone, 0, 4) . '-' . substr($telefone, 4);
            }
        }

        return [$cliente ?: 'Cliente não identificado', $telefone ?: null];
    }

    private function detectarTipo(?string $obs, string $clienteNome): string
    {
        $texto = ($obs ?? '') . ' ' . $clienteNome;

        foreach (self::PADROES_MENSALISTA as $padrao) {
            if (preg_match($padrao, $texto)) {
                $this->tiposCorrigidos++;
                return 'MENSALISTA';
            }
        }

        foreach (self::PADROES_FIXO as $padrao) {
            if (preg_match($padrao, $texto)) {
                $this->tiposCorrigidos++;
                return 'FIXA';
            }
        }

        return 'UNICA';
    }

    private function extrairValor(?string $obs): ?float
    {
        if (!$obs) return null;

        $padroes = [
            '/(?:valor|mensal(?:ista)?|avuls[oa]?|fixo)\s*[=:]\s*R?\$?\s*(\d{1,3}[\.,]?\d{0,3}[\.,]\d{2})/i',
            '/VALOR\s*[=:]\s*R?\$?\s*(\d{1,3}[\.,]?\d{0,3}[\.,]\d{2})/i',
            '/=\s*R?\$?\s*(\d{1,3}[\.,]?\d{0,3}[\.,]\d{2})/i',
            '/VALOR\s+(?:PARA\s+)?(?:\d+\s*HORA[S]?\s*[=:]?\s*)(\d{1,3}[\.,]?\d{0,3}[\.,]\d{2})/i',
            '/TAXA\s+DE\s+(\d{1,3}[\.,]?\d{0,3}[\.,]\d{2})/i',
        ];

        foreach ($padroes as $padrao) {
            if (preg_match($padrao, $obs, $m)) {
                $valor = $this->parsearValorMonetario($m[1]);
                if ($valor > 0 && $valor < 100000) {
                    $this->valoresExtraidos++;
                    return $valor;
                }
            }
        }

        return null;
    }

    private function parsearValorMonetario(string $str): float
    {
        $str = trim($str);

        if (preg_match('/^(\d{1,3})\.(\d{3}),(\d{2})$/', $str)) {
            $str = str_replace('.', '', $str);
            $str = str_replace(',', '.', $str);
        } elseif (preg_match('/^(\d{1,3}),(\d{2})$/', $str)) {
            $str = str_replace(',', '.', $str);
        } elseif (str_contains($str, ',')) {
            $str = str_replace('.', '', $str);
            $str = str_replace(',', '.', $str);
        }

        return (float) $str;
    }

    private function extrairDuracao(string $clienteRaw, ?string $obs): ?int
    {
        $texto = $clienteRaw . ' ' . ($obs ?? '');

        if (preg_match('/(\d+)\s*(?:hora[s]?)\s*e\s*meia/i', $texto, $m)) {
            return ((int) $m[1]) * 60 + 30;
        }

        if (preg_match('/(\d+)\s*[Hh]\s*(\d+)/i', $texto, $m)) {
            $horas = (int) $m[1];
            $minutos = (int) $m[2];
            if ($horas >= 1 && $horas <= 4 && $minutos > 0 && $minutos < 60) {
                return $horas * 60 + $minutos;
            }
        }

        if (preg_match('/(\d{1,2})[Hh](\d{2})\s*(?:AS|ÀS|às|as|A)\s*(\d{1,2})[Hh](\d{2})/i', $texto, $m)) {
            $inicio = (int) $m[1] * 60 + (int) $m[2];
            $fim = (int) $m[3] * 60 + (int) $m[4];
            if ($fim > $inicio) {
                return $fim - $inicio;
            }
        }

        if (preg_match('/(\d{1,2})h?\s*(?:AS|ÀS|às|as)\s*(\d{1,2})h/i', $texto, $m)) {
            $inicio = (int) $m[1] * 60;
            $fim = (int) $m[2] * 60;
            if ($fim > $inicio && ($fim - $inicio) <= 300) {
                return $fim - $inicio;
            }
        }

        return null;
    }

    private function calcularHorarios(string $horarioRaw, ?int $duracaoMin): array
    {
        $horario = substr($horarioRaw, 0, 5);
        $hora = (int) substr($horario, 0, 2);
        $minuto = (int) substr($horario, 3, 2);

        if (!$duracaoMin || $duracaoMin <= 60) {
            $fimH = $hora + 1;
            $fimHStr = str_pad($fimH % 24, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minuto, 2, '0', STR_PAD_LEFT);
            return [$horario, $fimHStr, 1];
        }

        $slotsNecessarios = (int) ceil($duracaoMin / 60);
        $fimH = $hora + $slotsNecessarios;
        $fimHStr = str_pad($fimH % 24, 2, '0', STR_PAD_LEFT) . ':' . str_pad($minuto, 2, '0', STR_PAD_LEFT);

        return [$horario, $fimHStr, $slotsNecessarios];
    }

    private function obterOuCriarCliente(string $nome, ?string $telefone): Cliente
    {
        $nomeNorm = $this->normalizarNomeCliente($nome);

        if (empty($nomeNorm)) {
            $nomeNorm = 'Cliente Não Identificado';
        }

        $chave = mb_strtolower($nomeNorm) . '|' . ($telefone ?? '');

        if (isset($this->clientesCache[$chave])) {
            return $this->clientesCache[$chave];
        }

        $query = Cliente::withTrashed()->where(DB::raw('LOWER(nome)'), mb_strtolower($nomeNorm));

        if ($telefone) {
            $foneNumeros = preg_replace('/[^\d]/', '', $telefone);
            $query->orWhere(DB::raw("REPLACE(REPLACE(telefone, '-', ''), ' ', '')"), $foneNumeros);
        }

        $cliente = $query->first();

        if (!$cliente) {
            $cliente = Cliente::create([
                'nome'     => $nomeNorm,
                'telefone' => $telefone,
            ]);
            $this->clientesCriados++;
        } elseif ($telefone && !$cliente->telefone) {
            $cliente->update(['telefone' => $telefone]);
        }

        $this->clientesCache[$chave] = $cliente;

        return $cliente;
    }

    private function normalizarNomeCliente(string $nome): string
    {
        $nome = preg_replace('/\s*\(?(fixo|mensal|FIXO|MENSAL|Fixo|Mensal|HORA AVULSA?|hora avulsa?|vôlei|VÔLEI|FUTEBOL|futebol)\)?\s*$/i', '', $nome);
        $nome = preg_replace('/\s*[-–]\s*(FESTA INFANTIL|festa infantil)\s*$/i', '', $nome);
        $nome = preg_replace('/\s*festa\s+infantil\s*$/i', '', $nome);
        $nome = preg_replace('/\s*[-–]\s*$/', '', $nome);
        $nome = preg_replace('/\s*\/\s*$/', '', $nome);
        $nome = preg_replace('/\s+/', ' ', trim($nome));

        return Str::title(mb_strtolower($nome));
    }

    private function limparObs(?string $obs): ?string
    {
        if (!$obs) return null;

        $linhas = explode("\n", $obs);
        $linhasLimpas = [];

        foreach ($linhas as $linha) {
            $linha = trim($linha);
            if (empty($linha)) continue;
            $linhasLimpas[] = $linha;
        }

        return empty($linhasLimpas) ? null : implode("\n", $linhasLimpas);
    }

    private function gerarObsSistema(?int $duracao, int $slots, int $idOriginal): string
    {
        $partes = ["Importado do sistema antigo (ID: {$idOriginal})"];

        if ($duracao && $duracao % 60 !== 0) {
            $horas = intdiv($duracao, 60);
            $minutos = $duracao % 60;
            $sobra = ($slots * 60) - $duracao;
            $partes[] = "Duração real: {$horas}h{$minutos}min ({$sobra}min não utilizados)";
        }

        return implode(' | ', $partes);
    }

    private function extrairPagamentos(?string $obs, Cliente $cliente, Reserva $reserva, string $createdAt): void
    {
        if (!$obs) return;

        if (preg_match_all('/cr[eé]dito\s*[=:]\s*(\d{1,3}[\.,]?\d{0,3}[\.,]\d{2})/i', $obs, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $valor = $this->parsearValorMonetario($m[1]);
                if ($valor > 0) {
                    Pagamento::create([
                        'cliente_id'     => $cliente->id,
                        'reserva_id'     => $reserva->id,
                        'tipo'           => 'CREDITO',
                        'valor'          => $valor,
                        'status'         => 'PAGO',
                        'obs'            => 'Importado do sistema antigo',
                        'data_pagamento' => Carbon::parse($createdAt)->toDateString(),
                        'created_at'     => $createdAt,
                        'updated_at'     => $createdAt,
                    ]);
                    $this->pagamentosCriados++;
                }
            }
        }

        if (preg_match_all('/d[eé]bito\s*[=:]\s*(\d{1,3}[\.,]?\d{0,3}[\.,]\d{2})/i', $obs, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $valor = $this->parsearValorMonetario($m[1]);
                if ($valor > 0) {
                    Pagamento::create([
                        'cliente_id' => $cliente->id,
                        'reserva_id' => $reserva->id,
                        'tipo'       => 'DEBITO',
                        'valor'      => $valor,
                        'status'     => 'PENDENTE',
                        'obs'        => 'Importado do sistema antigo',
                        'created_at' => $createdAt,
                        'updated_at' => $createdAt,
                    ]);
                    $this->pagamentosCriados++;
                }
            }
        }

        if (preg_match_all('/[Pp]ago\s+(?:R?\$?\s*)?(\d{1,3}[\.,]?\d{0,3}[\.,]\d{2})\s*(?:dia\s+)?(\d{1,2}\/\d{2})?/i', $obs, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $m) {
                $valor = $this->parsearValorMonetario($m[1]);
                if ($valor > 0) {
                    Pagamento::create([
                        'cliente_id'     => $cliente->id,
                        'reserva_id'     => $reserva->id,
                        'tipo'           => 'PAGAMENTO',
                        'valor'          => $valor,
                        'status'         => 'PAGO',
                        'obs'            => 'Importado do sistema antigo',
                        'data_pagamento' => Carbon::parse($createdAt)->toDateString(),
                        'created_at'     => $createdAt,
                        'updated_at'     => $createdAt,
                    ]);
                    $this->pagamentosCriados++;
                }
            }
        }
    }

    private function parsearData(?string $dataStr): ?string
    {
        if (!$dataStr) return null;

        try {
            return Carbon::parse(str_replace('.000', '', $dataStr))->toDateString();
        } catch (\Exception) {
            return null;
        }
    }

    private function registrarErro(int $id, string $mensagem): void
    {
        $this->erros++;
        $this->errosDetalhados[] = "ID {$id}: {$mensagem}";
    }

    private function limparCaches(): void
    {
        try {
            Cache::tags(Reserva::CACHE_TAG)->flush();
            Cache::tags(Area::CACHE_TAG)->flush();
            Cache::tags(Cliente::CACHE_TAG)->flush();
            Cache::tags(Pagamento::CACHE_TAG)->flush();
            $this->info('▸ Caches limpos');
        } catch (\Exception $e) {
            $this->warn("▸ Não foi possível limpar cache: {$e->getMessage()}");
        }
    }

    private function exibirRelatorio(): void
    {
        $this->newLine();
        $this->info('═══════════════════════════════════════════════');
        $this->info(' RELATÓRIO DE IMPORTAÇÃO');
        $this->info('═══════════════════════════════════════════════');
        $this->newLine();

        $this->table(
            ['Métrica', 'Quantidade'],
            [
                ['Clientes criados', $this->clientesCriados],
                ['Reservas importadas', $this->reservasImportadas],
                ['Pagamentos criados', $this->pagamentosCriados],
                ['Telefones extraídos do nome', $this->telefonesExtraidos],
                ['Tipos corrigidos (UNICA→FIXA/MENSAL)', $this->tiposCorrigidos],
                ['Valores extraídos do obs', $this->valoresExtraidos],
                ['Erros', $this->erros],
            ],
        );

        if (!empty($this->errosDetalhados)) {
            $this->newLine();
            $this->warn('Erros detalhados:');
            foreach (array_slice($this->errosDetalhados, 0, 50) as $erro) {
                $this->line("  • {$erro}");
            }
            if (count($this->errosDetalhados) > 50) {
                $this->line('  ... e mais ' . (count($this->errosDetalhados) - 50) . ' erros');
            }
        }

        $this->newLine();
        $this->info('▸ Importação concluída.');
    }
}
