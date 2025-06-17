<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $superadmin = Role::create([
            'name'          => 'superadmin'
        ]);
        $superadmin->givePermissionTo([
            'user.delete',
            'user.update',
            'user.read',
            'user.create',
            'role.delete',
            'role.update',
            'role.read',
            'role.create',
            'permission.delete',
            'permission.update',
            'permission.read',
            'permission.create'
        ]);
        $admin = Role::create([
            'name'          => 'admin'
        ]);
        $admin->givePermissionTo([
            'user.delete',
            'user.update',
            'user.read',
            'user.create',
            'role.read',
            'permission.read',
        ]);
        $operator = Role::create([
            'name'          => 'operator'
        ]);

        $operator->givePermissionTo([
            'user.read',
            'user.create',
            'role.read',
            'permission.read',
        ]);
    }
}
