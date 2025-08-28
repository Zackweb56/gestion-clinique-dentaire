<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class DefaultRoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء دور admin إذا لم يكن موجوداً
        $adminRole = Role::firstOrCreate(['name' => 'Administrateur']);

        // الحصول على كل الصلاحيات
        $permissions = Permission::all();

        // ربط كل الصلاحيات بدور admin
        $adminRole->syncPermissions($permissions);
    }
}