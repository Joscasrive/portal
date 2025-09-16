<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
      
        Permission::create(['name' => 'see-user']);
        Permission::create(['name' => 'delete-user']);
        Permission::create(['name' => 'update-user']);
        Permission::create(['name' => 'crear-user']);
        
       
        Permission::create(['name' => 'view company']);
        Permission::create(['name' => 'view phone']);
        Permission::create(['name' => 'view total referred users']);
        Permission::create(['name' => 'view email']);

     
        $adminUser = User::query()->create([
            'name' => 'admin',
            'email' => 'support@credfixx.com',
            'password' => Hash::make('12345'),
            'phone' => '+584245453940',
            'company' => 'CredFixx',
            'email_verified_at' => now()
        ]);
        $roleAdmin = Role::create(['name' => 'admin']);
        $adminUser->assignRole($roleAdmin);
       
        $permissionAdmin = Permission::query()->pluck('name');
        $roleAdmin->syncPermissions($permissionAdmin);
          
        
        $partnerUser = User::query()->create([
            'name' => 'partner',
            'email' => 'partner@credfixx.com',
            'password' => Hash::make('123456'),
            'phone' => '+584245453940',
            'company' => 'CredFixx',
            'email_verified_at' => now()
        ]);
        $rolePartner = Role::create(['name' => 'partner']);
        $partnerUser->assignRole($rolePartner);
      
        $rolePartner->syncPermissions(['crear-user', 'see-user']);
    }
}