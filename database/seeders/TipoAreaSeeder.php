<?php

namespace Database\Seeders;

use App\Models\TipoArea;
use Illuminate\Database\Seeder;

class TipoAreaSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nome' => 'QUADRA', 'icone' => 'fi-rr-football', 'cor' => '#10b981'],
            ['nome' => 'CHURRASQUEIRA', 'icone' => 'fi-rr-grill', 'cor' => '#f59e0b'],
        ];

        foreach ($tipos as $tipo) {
            TipoArea::firstOrCreate(['nome' => $tipo['nome']], $tipo);
        }
    }
}