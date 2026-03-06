<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;

class UserSeeder extends Seeder
{
    public function run(): void
    {
        $admin = User::firstOrCreate(
            ['email' => env('ADMIN_EMAIL')],
            [
                'name'     => 'Administrador',
                'password' => env('ADMIN_PASSWORD'),
            ],
        );

        $admin->syncRoles(['admin']);
    }
}