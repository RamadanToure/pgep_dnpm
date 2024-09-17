<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use App\Models\Service;
use App\Models\{TypeService, Role, EtapeTypeDemande, TypeDemande, Etape, TypeDocument, TypeDemandeTypeDocumentEtape};
use App\Models\Utilisateur;
use Illuminate\Support\Facades\Hash;

class ServicesSeeder extends Seeder
{

    function createUser($role) {

        return Utilisateur::whereIn('role_id', function ($query) use ($role) {
            $query->from("role")->whereNom($role)->select("id")->get();
        })->first();
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {

        $services = [
            "Ministre" => [
                "role" => "ministre"
            ],
            "Chef de cabinet" => [
                "role" => "cabinet"
            ],
            "Secrétariat Général" => [
                "role" => "sg"
            ],
            "Conseiller juridique" => [
                "role" => "juridique"
            ],
            "DIRECTION NATIONALE DE LA PHARMACIE ET DU MEDICAMENT" => [
                "SIGLE" => "DNPM",
                "AGREMENT 1" => [
                    'Copie de Registre du Commerce',
                    'Demande manuscrite au Ministre de sante',
                ],
                "AGREMENT 2" => [
                    'Demande adressé au Ministre',
                ],

            ],

        ];

        $typeService = TypeService::create([
            'nom' => "Direction nationale",
            'uuid' => Str::uuid()
        ]);

        $typeService_2 = TypeService::create([
            'nom' => "Service central",
            'uuid' => Str::uuid()
        ]);

        $typeServiceDivision = TypeService::create([
            'nom' => "Division",
            'uuid' => Str::uuid()
        ]);

        $etape = Etape::create([
            'nom' => "Soumission des dossiers",
            'uuid' => Str::uuid()
        ]);

        $etape_2 = Etape::create([
            'nom' => "Traitement des documents",
            'uuid' => Str::uuid()
        ]);

        $etape_3 = Etape::create([
            'nom' => "Projet d'agrément",
            'uuid' => Str::uuid()
        ]);

        $i_22 = 0;

        foreach ($services as $key => $types) {

            $i_22++;

            $createdService = Service::create([
                'uuid' => Str::uuid(),
                'nom' => $key,
                'is_central' => $i_22 <= 4,
                'sigle' => isset($types['SIGLE']) ? $types['SIGLE']:$key,
                'type_service_id' => $i_22 <= 4 ? $typeService_2->id:$typeService->id
            ]);

            if(isset($types['role'])) {
                $createdService->update([
                    'utilisateur_id' => $this->createUser($types['role'])->id
                ]);
            }

            if($i_22 > 4) {

                for ($i=1; $i < 4; $i++) {

                    Service::create([
                        'parent_id' => $createdService->id,
                        'uuid' => Str::uuid(),
                        'nom' => "Division $i - ".$createdService->sigle,
                        'sigle' => "",
                        'type_service_id' => $typeServiceDivision->id
                    ]);

                }
            }

            //Type de demandes
            foreach ($types as $nom => $documents) {
                if($nom == "SIGLE" OR $nom == "role") continue;
                $typeDemande = TypeDemande::create([
                    'uuid' => Str::uuid(),
                    'nom' => $nom,
                    'service_id' => $createdService->id
                ]);

                EtapeTypeDemande::create([
                    'uuid' => Str::uuid(),
                    'etape_id' => $etape->id,
                    'type_demande_id' => $typeDemande->id,
                    'ordre' => 1,
                ]);

                EtapeTypeDemande::create([
                    'uuid' => Str::uuid(),
                    'etape_id' => $etape_2->id,
                    'type_demande_id' => $typeDemande->id,
                    'ordre' => 2,
                    'is_traitement' => true,
                ]);

                EtapeTypeDemande::create([
                    'uuid' => Str::uuid(),
                    'etape_id' => $etape_3->id,
                    'type_demande_id' => $typeDemande->id,
                    'ordre' => 3,
                    'is_agrement' => true
                ]);

                foreach ($documents as $key => $document) {

                    $typeDocument = TypeDocument::firstOrCreate([
		                'nom' => $document
                    ]);

                    if(!$typeDocument->uuid) $typeDocument->update(['uuid' => Str::uuid()]);

                    TypeDemandeTypeDocumentEtape::create([
                        'etape_id' => $etape->id,
                        'type_demande_id' => $typeDemande->id,
                        'type_document_id' => $typeDocument->id
                    ]);
                }
            }
        }

        TypeService::create([
            'nom' => "Service central",
            'uuid' => Str::uuid()
        ]);
    }
}
