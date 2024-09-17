<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SeederPermission extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            'view_user','create_user', 'edit_user', 'delete_user',
            'view_role','create_role', 'edit_role', 'delete_role',
            'view_permission','create_permission', 'edit_permission', 'delete_permission',
            'view_type_demande','create_type_demande', 'edit_type_demande', 'delete_type_demande',
            'view_demande','create_demande', 'edit_demande', 'delete_demande',
            'view_type_document','create_type_document', 'edit_type_document', 'delete_type_document',
            'view_document','create_document', 'edit_document', 'delete_document',
        ];

        for($i = 0; $i < count($permissions); $i++) {
            DB::table('permission')->insert([
                'nom' => $permissions[$i],
            ]);
        }
    }
}
