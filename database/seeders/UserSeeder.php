<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'nom' => 'Admin',
            'prenom' => 'Principal',
            'email' => 'admin@smarttransport.com',
            'telephone' => '770000000',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
        ]);

        User::create([
            'nom' => 'Agent',
            'prenom' => 'Terminal',
            'email' => 'terminal@smarttransport.com',
            'telephone' => '771111111',
            'password' => Hash::make('terminal123'),
            'role' => 'agent_terminal',
        ]);

        User::create([
            'nom' => 'Agent',
            'prenom' => 'Bagagiste',
            'email' => 'bagagiste@smarttransport.com',
            'telephone' => '772222222',
            'password' => Hash::make('bagagiste123'),
            'role' => 'agent_bagagiste',
        ]);
    }
}
