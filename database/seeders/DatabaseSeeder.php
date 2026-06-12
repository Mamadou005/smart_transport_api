<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder {
    public function run(): void {
        $this->call([UserSeeder::class]);

        // Voyages de test
        DB::table('voyages')->insert([
            [
                'origine'        => 'Dakar',
                'destination'    => 'Thiès',
                'date_depart'    => '2026-05-10 08:00:00',
                'date_arrivee'   => '2026-05-10 10:00:00',
                'type_transport' => 'routier',
                'statut'         => 'planifie',
                'capacite'       => 50,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'origine'        => 'Dakar',
                'destination'    => 'Saint-Louis',
                'date_depart'    => '2026-05-12 07:30:00',
                'date_arrivee'   => '2026-05-12 11:30:00',
                'type_transport' => 'routier',
                'statut'         => 'planifie',
                'capacite'       => 40,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'origine'        => 'Dakar',
                'destination'    => 'Ziguinchor',
                'date_depart'    => '2026-05-15 06:00:00',
                'date_arrivee'   => '2026-05-15 14:00:00',
                'type_transport' => 'aerien',
                'statut'         => 'planifie',
                'capacite'       => 120,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
            [
                'origine'        => 'Thiès',
                'destination'    => 'Dakar',
                'date_depart'    => '2026-05-11 09:00:00',
                'date_arrivee'   => '2026-05-11 11:00:00',
                'type_transport' => 'ferroviaire',
                'statut'         => 'planifie',
                'capacite'       => 200,
                'created_at'     => now(),
                'updated_at'     => now(),
            ],
        ]);
    }
}
