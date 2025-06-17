<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PermissionsLotterySeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'section' => 'Lottery',
                'name' => 'lottery.results_index',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Lottery',
                'name' => 'lottery.results_create',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Lottery',
                'name' => 'lottery.results_update',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Lottery',
                'name' => 'lottery.results_delete',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Lottery',
                'name' => 'lottery.combinations_index',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Lottery',
                'name' => 'lottery.magik_numbers_create',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Lottery',
                'name' => 'lottery.results_import',
                'guard_name' => 'web'
            ],
        ];

        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['name' => $permission['name'], 'guard_name' => $permission['guard_name'], 'section' => $permission['section']]
            );
        }

        $superadmin = Role::where('name','superadmin')->first();
        $superadmin->givePermissionTo([
            array_column($permissions, 'name')
        ]);
    }
}
