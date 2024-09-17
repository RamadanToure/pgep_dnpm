<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Utilisateur, TypeOng};
use Illuminate\Support\Str;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            RegionSeeder::class,
            SeederPermission::class,
            SeedUtilisateur::class,
            ServicesSeeder::class,
            // RegionSeeder::class,
            // TypePaiementSeeder::class
        ]);


    }
}
