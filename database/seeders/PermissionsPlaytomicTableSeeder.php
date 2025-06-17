<?php

namespace Database\Seeders;

use App\Models\Permission;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;

class PermissionsPlaytomicTableSeeder extends Seeder
{
    public function run()
    {
        $permissions = [
            [
                'section' => 'Playtomic',
                'name' => 'playtomic.club_index',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Playtomic',
                'name' => 'playtomic.club_create',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Playtomic',
                'name' => 'playtomic.club_show',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Playtomic',
                'name' => 'playtomic.club_edit',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Playtomic',
                'name' => 'playtomic.club_delete',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Playtomic',
                'name' => 'playtomic.timetable_index',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Playtomic',
                'name' => 'playtomic.timetable_show',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Playtomic',
                'name' => 'playtomic.timetable_create',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Playtomic',
                'name' => 'playtomic.timetable_edit',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Playtomic',
                'name' => 'playtomic.timetable_delete',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Playtomic',
                'name' => 'playtomic.resource_index',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Playtomic',
                'name' => 'playtomic.resource_show',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Playtomic',
                'name' => 'playtomic.resource_create',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Playtomic',
                'name' => 'playtomic.resource_edit',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Playtomic',
                'name' => 'playtomic.resource_delete',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Playtomic',
                'name' => 'playtomic.booking_index',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Playtomic',
                'name' => 'playtomic.booking_show',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Playtomic',
                'name' => 'playtomic.booking_create',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Playtomic',
                'name' => 'playtomic.booking_edit',
                'guard_name' => 'web'
            ],
            [
                'section' => 'Playtomic',
                'name' => 'playtomic.booking_delete',
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
