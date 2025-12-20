<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;

class AdminSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $user = User::firstOrCreate(
            ['email' => 'admin@desa.id'],
            [
                'name' => 'Administrator',
                'password' => bcrypt('admin123')
            ]
        );

        $user->assignRole('admin');
    }
}
