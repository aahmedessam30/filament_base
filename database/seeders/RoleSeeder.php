<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;

class RoleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $roles = [
            'super_admin' => ['permissions' => Permission::all()],
            'admin'       => ['permissions' => Permission::all()],
        ];

        foreach ($roles as $role => $data) {
            $ceatedRole = Role::create(['name' => $role, 'guard_name' => $data['guard'] ?? 'web']);
            if (isset($data['permissions'])) {
                $ceatedRole->syncPermissions($data['permissions']);
            }
        }
    }
}
