<?php

namespace Database\Seeders;

use Illuminate\Support\Str;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Models\{Demande, Utilisateur, Role, RolePermission, Permission};

class SeedUtilisateur extends Seeder
{

    function getPermissionByName($nom)
    {
        return Permission::whereNom($nom)->first();
    }
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = array(
            'admin' => 'Administrateur',
            'cabinet' => 'Chef de cabinet',
            'sg' => 'Secrétaire général',
            'juridique' => "Conseiller juridique",
            'ministre' => 'Ministre',
            'demandeur' => 'Demandeur',
            'consultant' => 'Consultant'
        );

        foreach ($roles as $key => $role) {

            Role::create([
                'uuid' => Str::uuid(),
                'nom' => $key,
                'description' => $role
            ]);
        }

        $role = Role::whereNom("admin");

        Utilisateur::create([
            'email_verified_at' => now(),
            'uuid' => Str::uuid(),
            'nom' => 'Général',
            'prenom' => 'Administrateur',
            'adresse' => 'Kipe',
            'telephone'=>'622000000',
            'email' => 'admin@mshp.gov.gn',
            'role_id' => $role->exists() ? $role->first()->id:null,
            'is_valide' => true,
            'status_compte' => true,
            'is_deleted' => 0,
            'is_root' => true,
            'password'=> Hash::make(1234)
        ]);

        $role = Role::whereNom("cabinet");

        Utilisateur::create([
            'email_verified_at' => now(),
            'uuid' => Str::uuid(),
            'nom' => 'Cabinet',
            'prenom' => 'Chef',
            'adresse' => 'Kipe',
            'telephone'=>'622000000',
            'email' => 'cabinet@mshp.gov.gn',
            'role_id' => $role->exists() ? $role->first()->id:null,
            'is_valide' => true,
            'status_compte' => true,
            'is_deleted' => 0,
            'is_root' => true,
            'password'=> Hash::make(1234)
        ]);

        $role = Role::whereNom("sg");

        Utilisateur::create([
            'email_verified_at' => now(),
            'uuid' => Str::uuid(),
            'nom' => 'General',
            'prenom' => 'Secretaire',
            'adresse' => 'Kipe',
            'telephone'=>'622000000',
            'email' => 'sg@mshp.gov.gn',
            'role_id' => $role->exists() ? $role->first()->id:null,
            'is_valide' => true,
            'status_compte' => true,
            'is_deleted' => 0,
            'is_root' => true,
            'password'=> Hash::make(1234)
        ]);

        $role = Role::whereNom("juridique");

        Utilisateur::create([
            'email_verified_at' => now(),
            'uuid' => Str::uuid(),
            'nom' => 'Juridique',
            'prenom' => 'Conseiller',
            'adresse' => 'Kipe',
            'telephone'=> '622000000',
            'email' => 'juridique@mshp.gov.gn',
            'role_id' => $role->exists() ? $role->first()->id:null,
            'is_valide' => true,
            'status_compte' => true,
            'is_deleted' => 0,
            'is_root' => true,
            'password'=> Hash::make(1234)
        ]);

        $role = Role::whereNom("ministre");

        Utilisateur::create([
            'email_verified_at' => now(),
            'uuid' => Str::uuid(),
            'nom' => 'Minsitre',
            'prenom' => 'Ministre',
            'adresse' => 'Kipe',
            'telephone'=> '622000000',
            'email' => 'ministre@mshp.gov.gn',
            'role_id' => $role->exists() ? $role->first()->id:null,
            'is_valide' => true,
            'status_compte' => true,
            'is_deleted' => 0,
            'is_root' => true,
            'password'=> Hash::make(1234)
        ]);

        if($role->first()) {

            foreach (['view_demande', 'create_demande', 'edit_demande', 'delete_demande'] as $key => $value) {

                RolePermission::create([
                    'role_id' => $role->first()->id,
                    'permission_id' => $this->getPermissionByName($value)->id
                ]);
            }
        }

        $role = Role::whereNom("consultant");

        Utilisateur::create([
            'email_verified_at' => now(),
            'uuid' => Str::uuid(),
            'nom' => 'Consultant',
            'prenom' => 'Utilisateur',
            'adresse' => 'Kipe',
            'telephone'=>'622000000',
            'email' => 'consultant@mshp.gov.gn',
            'role_id' => $role->exists() ? $role->first()->id:null,
            'is_valide' => true,
            'status_compte' => true,
            'is_deleted' => 0,
            'is_root' => false,
            'password'=> Hash::make(1234)
        ]);

        if($role->first()) {

            foreach (['view_demande'] as $key => $value) {

                RolePermission::create([
                    'role_id' => $role->first()->id,
                    'permission_id' => $this->getPermissionByName($value)->id
                ]);
            }
        }

        $role = Role::whereNom("demandeur");

        Utilisateur::create([
            'email_verified_at' => now(),
            'uuid' => Str::uuid(),
            'nom' => 'Demandeur',
            'prenom' => 'Utilisateur',
            'adresse' => 'Kipe',
            'telephone'=>'622102134',
            'email' => 'demandeur@mshp.gov.gn',
            'role_id' => $role->exists() ? $role->first()->id:null,
            'is_valide' => true,
            'status_compte' => true,
            'is_deleted' => 0,
            'is_root' => false,
            'password'=> Hash::make(1234)
        ]);

        if($role->first()) {

            foreach (['view_demande', 'create_demande', 'edit_demande', 'delete_demande'] as $key => $value) {

                RolePermission::create([
                    'role_id' => $role->first()->id,
                    'permission_id' => $this->getPermissionByName($value)->id
                ]);
            }
        }

    }
}
