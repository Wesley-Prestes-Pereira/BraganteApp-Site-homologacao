<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => 'admin@bragante.com.br'],
            [
                'name'     => 'Administrador',
                'password' => 'bragante2025',
            ],
        );

        $admin->syncRoles(['admin']);
    }
}