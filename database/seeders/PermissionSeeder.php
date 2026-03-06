<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\{Permission, Role};

class PermissionSeeder extends Seeder
{
    public function run(): void
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        $modulos = [
            'areas'      => ['ver', 'criar', 'editar', 'excluir'],
            'reservas'   => ['ver', 'criar', 'editar', 'excluir'],
            'usuarios'   => ['ver', 'criar', 'editar', 'excluir'],
            'clientes'   => ['ver', 'criar', 'editar', 'excluir'],
            'financeiro' => ['ver', 'criar', 'editar', 'excluir'],
            'taxas'      => ['ver', 'criar', 'editar', 'excluir'],
            'valores'    => ['ver', 'criar', 'editar', 'excluir'],
        ];

        $todasPermissoes = [];

        foreach ($modulos as $modulo => $acoes) {
            foreach ($acoes as $acao) {
                $nome = "{$modulo}.{$acao}";
                Permission::firstOrCreate(['name' => $nome, 'guard_name' => 'web']);
                $todasPermissoes[] = $nome;
            }
        }

        $admin = Role::firstOrCreate(['name' => 'admin', 'guard_name' => 'web']);
        $admin->syncPermissions($todasPermissoes);

        $usuario = Role::firstOrCreate(['name' => 'usuario', 'guard_name' => 'web']);
        $usuario->syncPermissions([
            'areas.ver',
            'reservas.ver',
            'reservas.criar',
        ]);
    }
}