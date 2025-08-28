<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // إنشاء مستخدم المدير
        $admin = User::create([
            'name' => 'Boughaba Zakariyae',
            'email' => 'ziko@admin.com',
            'password' => Hash::make('00000000'), 
            'email_verified_at' => now(),
        ]);

        // تعيين دور Admin للمستخدم
        $admin->assignRole('Administrateur');
    }
}