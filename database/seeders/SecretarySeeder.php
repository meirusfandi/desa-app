<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class SecretarySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'sekretaris@desa.id'],
            [
                'name' => 'Sekretaris Desa',
                'password' => bcrypt('password123')
            ]
        );

        $user->assignRole('sekretaris');
    }
}
