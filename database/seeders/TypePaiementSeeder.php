<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{TypePaiement};
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;

//php artisan db:seed --class=TypePaiementSeeder
class TypePaiementSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $types = [
            "Frais d'étude",
            "Frais d'agrément",
            "Frais d'inspection"
        ];

        foreach ($types as $key => $type) {

            TypePaiement::create([
                'nom' => $type,
                'uuid' => Str::uuid()
            ]);
        }
    }
}
