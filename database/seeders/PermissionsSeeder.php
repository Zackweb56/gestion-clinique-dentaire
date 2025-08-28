<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $permissions = [
            ['name' => 'créer utilisateur', 'group' => 'utilisateurs'],
            ['name' => 'modifier utilisateur', 'group' => 'utilisateurs'],
            ['name' => 'supprimer utilisateur', 'group' => 'utilisateurs'],
            ['name' => 'voir utilisateurs', 'group' => 'utilisateurs'],
        
            ['name' => 'créer rôle', 'group' => 'rôles'],
            ['name' => 'modifier rôle', 'group' => 'rôles'],
            ['name' => 'supprimer rôle', 'group' => 'rôles'],
            ['name' => 'voir rôles', 'group' => 'rôles'],

            ['name' => 'créer service', 'group' => 'services'],
            ['name' => 'modifier service', 'group' => 'services'],
            ['name' => 'supprimer service', 'group' => 'services'],
            ['name' => 'voir services', 'group' => 'services'],

            ['name' => 'créer patient', 'group' => 'patients'],
            ['name' => 'modifier patient', 'group' => 'patients'],
            ['name' => 'supprimer patient', 'group' => 'patients'],
            ['name' => 'voir patients', 'group' => 'patients'],

            ['name' => 'créer dossier-médical', 'group' => 'dossiers-médicaux'],
            ['name' => 'modifier dossier-médical', 'group' => 'dossiers-médicaux'],
            ['name' => 'supprimer dossier-médical', 'group' => 'dossiers-médicaux'],
            ['name' => 'voir dossiers-médicaux', 'group' => 'dossiers-médicaux'],

            ['name' => 'créer rendez-vous', 'group' => 'rendez-vous'],
            ['name' => 'modifier rendez-vous', 'group' => 'rendez-vous'],
            ['name' => 'supprimer rendez-vous', 'group' => 'rendez-vous'],
            ['name' => 'voir rendez-vous', 'group' => 'rendez-vous'],

            ['name' => 'créer consultation', 'group' => 'consultations'],
            ['name' => 'modifier consultation', 'group' => 'consultations'],
            ['name' => 'supprimer consultation', 'group' => 'consultations'],
            ['name' => 'voir consultations', 'group' => 'consultations'],

            ['name' => 'créer acte', 'group' => 'actes'],
            ['name' => 'modifier acte', 'group' => 'actes'],
            ['name' => 'supprimer acte', 'group' => 'actes'],
            ['name' => 'voir actes', 'group' => 'actes'],

            ['name' => 'créer facture', 'group' => 'factures'],
            ['name' => 'modifier facture', 'group' => 'factures'],
            ['name' => 'supprimer facture', 'group' => 'factures'],
            ['name' => 'voir factures', 'group' => 'factures'],

        ];
        
        foreach ($permissions as $permission) {
            Permission::firstOrCreate([
                'name' => $permission['name'],
                'guard_name' => 'web', // use the correct guard
            ], [
                'group' => $permission['group'],
            ]);
        }
    }
}