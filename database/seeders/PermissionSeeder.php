<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class PermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Permission::create(['name' => 'user.delete']);
        Permission::create(['name' => 'user.update']);
        Permission::create(['name' => 'user.read']);
        Permission::create(['name' => 'user.create']);

        Permission::create(['name' => 'role.delete']);
        Permission::create(['name' => 'role.update']);
        Permission::create(['name' => 'role.read']);
        Permission::create(['name' => 'role.create']);

        Permission::create(['name' => 'permission.delete']);
        Permission::create(['name' => 'permission.update']);
        Permission::create(['name' => 'permission.read']);
        Permission::create(['name' => 'permission.create']);
    }
}
