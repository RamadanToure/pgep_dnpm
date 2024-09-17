<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\{Utilisateur, TypeOng, Region, Prefecture, Commune};
use Illuminate\Support\Str;

class RegionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        // $regions = \config("region");

        // Region::whereNotNull("nom")->delete();

        // if (!Region::count()) {

        //     foreach ($regions as $key => $prefectures) {
        //         $region = Region::firstOrCreate([
        //             'nom' => $key,
        //             'uuid' => Str::uuid(),
        //             'slug' => Str::slug($key)
        //         ]);

        //         foreach ($prefectures as $key => $communes) {
        //             $prefecture = Prefecture::firstOrCreate([
        //                 'nom' => $key,
        //                 'uuid' => Str::uuid(),
        //                 'slug' => Str::slug($key),
        //                 'region_id' => $region->id
        //             ]);


        //             foreach ($communes as $key => $commune) {
        //                 Commune::firstOrCreate([
        //                     'nom' => $commune,
        //                     'uuid' => Str::uuid(),
        //                     'slug' => Str::slug($commune),
        //                     'prefecture_id' => $prefecture->id
        //                 ]);
        //             }
        //         }
        //     }
        // }
    }
}
