<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\User;

class KepalaDesaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'kepala@desa.id'],
            [
                'name' => 'Kepala Desa',
                'password' => bcrypt('password123'),
            ]
        );

        if (! $user->hasRole('kepala_desa')) {
            $user->assignRole('kepala_desa');
        }
    }
}
