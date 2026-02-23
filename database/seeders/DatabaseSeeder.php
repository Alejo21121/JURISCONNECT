<?php

namespace Database\Seeders;

use App\Models\Role;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DatabaseSeeder extends \Illuminate\Database\Seeder
{
    
public function run(): void
{
    $this->call([
            RoleSeeder::class,    
            AbogadoUserSeeder::class,  
            ProcesoSeeder::class,
            LawyerSeeder::class,
            AssistantSeeder::class,
            AssistantLawyerSeeder::class,
        ]);

    // Crear rol de administrador
    $adminRole = Role::firstOrCreate([
        'name' => 'admin'
    ]);

    // Crear un usuario con ese rol
    User::firstOrCreate([
        
        'email' => 'brendaModa45@gmail.com'

    ], [
        'name' => 'admin',
        'password' => Hash::make('admin123'), 
        'role_id' => $adminRole->id,
        'remember_token' => Str::random(10),
    ]);
}
}