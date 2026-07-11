<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define permissions
        $permissions = [
            'view dashboard',
            'manage users',
            'manage roles',
            'manage settings',
            'create surveys',
            'edit surveys',
            'delete surveys',
            'view reports',
            'export reports',
            'collect data',
            'manage payments',
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::findOrCreate($permission, 'web');
        }

        // Reset cached roles and permissions again to load new permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Define roles and assign permissions
        $rolesConfig = [
            'Super Admin' => $permissions,
            'Admin' => [
                'view dashboard',
                'manage users',
                'manage roles',
                'manage settings',
                'create surveys',
                'edit surveys',
                'view reports',
                'export reports',
                'collect data',
            ],
            'Project Manager' => [
                'view dashboard',
                'create surveys',
                'edit surveys',
                'view reports',
                'export reports',
            ],
            'Field Manager' => [
                'view dashboard',
                'collect data',
                'view reports',
            ],
            'Field Agent' => [
                'collect data',
            ],
            'Client' => [
                'view dashboard',
                'view reports',
                'export reports',
            ],
            'Panelist' => [
                'view dashboard',
            ],
        ];

        foreach ($rolesConfig as $roleName => $rolePermissions) {
            $role = Role::findOrCreate($roleName, 'web');
            $role->syncPermissions($rolePermissions);
        }

        // Create default Admin User
        $admin = User::updateOrCreate(
            ['email' => 'admin@metricapolls.com'],
            [
                'name' => 'Metrica Administrator',
                'password' => Hash::make('Password123'),
                'email_verified_at' => now(),
                'status' => 'active',
            ]
        );
        $admin->assignRole('Super Admin');

        // Create default Client User
        $client = User::updateOrCreate(
            ['email' => 'client@metricapolls.com'],
            [
                'name' => 'Acme Corporation Client',
                'password' => Hash::make('Password123'),
                'email_verified_at' => now(),
                'status' => 'active',
            ]
        );
        $client->assignRole('Client');

        // Create default Panelist User
        $panelist = User::updateOrCreate(
            ['email' => 'panelist@metricapolls.com'],
            [
                'name' => 'John Doe Respondent',
                'password' => Hash::make('Password123'),
                'email_verified_at' => now(),
                'status' => 'active',
            ]
        );
        $panelist->assignRole('Panelist');
    }
}
