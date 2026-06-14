<?php

namespace Database\Seeders;

use App\Models\Permission;
use App\Models\Role;
use Illuminate\Database\Seeder;

class RoleAndPermissionSeeder extends Seeder
{
    public function run(): void
    {
        $permissions = [
            // User Management
            ['name' => 'View Users', 'slug' => 'view-users', 'module' => 'User Management', 'description' => 'View list of users'],
            ['name' => 'Create User', 'slug' => 'create-user', 'module' => 'User Management', 'description' => 'Create new user'],
            ['name' => 'Edit User', 'slug' => 'edit-user', 'module' => 'User Management', 'description' => 'Edit user details'],
            ['name' => 'Delete User', 'slug' => 'delete-user', 'module' => 'User Management', 'description' => 'Delete user'],
            ['name' => 'Toggle User Status', 'slug' => 'toggle-user-status', 'module' => 'User Management', 'description' => 'Activate/Deactivate user'],

            // Roles & Permissions
            ['name' => 'View Roles', 'slug' => 'view-roles', 'module' => 'Roles & Permissions', 'description' => 'View list of roles'],
            ['name' => 'Create Role', 'slug' => 'create-role', 'module' => 'Roles & Permissions', 'description' => 'Create new role'],
            ['name' => 'Edit Role', 'slug' => 'edit-role', 'module' => 'Roles & Permissions', 'description' => 'Edit role details'],
            ['name' => 'Delete Role', 'slug' => 'delete-role', 'module' => 'Roles & Permissions', 'description' => 'Delete role'],
            ['name' => 'Manage Role Permissions', 'slug' => 'manage-role-permissions', 'module' => 'Roles & Permissions', 'description' => 'Assign permissions to roles'],
            ['name' => 'Assign Role to User', 'slug' => 'assign-role-user', 'module' => 'Roles & Permissions', 'description' => 'Assign roles to users'],

            // System Settings
            ['name' => 'View Settings', 'slug' => 'view-settings', 'module' => 'System Settings', 'description' => 'View system settings'],
            ['name' => 'Update Settings', 'slug' => 'update-settings', 'module' => 'System Settings', 'description' => 'Update system settings'],

            // Backup & Restore
            ['name' => 'View Backups', 'slug' => 'view-backups', 'module' => 'Backup & Restore', 'description' => 'View backup list'],
            ['name' => 'Create Backup', 'slug' => 'create-backup', 'module' => 'Backup & Restore', 'description' => 'Create database backup'],
            ['name' => 'Restore Backup', 'slug' => 'restore-backup', 'module' => 'Backup & Restore', 'description' => 'Restore database from backup'],
            ['name' => 'Delete Backup', 'slug' => 'delete-backup', 'module' => 'Backup & Restore', 'description' => 'Delete backup file'],

            // Audit Logs
            ['name' => 'View Audit Logs', 'slug' => 'view-audit-logs', 'module' => 'Audit Logs', 'description' => 'View audit trail'],
            ['name' => 'Export Audit Logs', 'slug' => 'export-audit-logs', 'module' => 'Audit Logs', 'description' => 'Export audit logs'],

            // Reports
            ['name' => 'View Reports', 'slug' => 'view-reports', 'module' => 'Reports', 'description' => 'View reports'],
            ['name' => 'Export Reports', 'slug' => 'export-reports', 'module' => 'Reports', 'description' => 'Export reports to PDF/Excel'],
        ];

        // Create permissions
        foreach ($permissions as $permission) {
            Permission::firstOrCreate(
                ['slug' => $permission['slug']],
                [
                    'name' => $permission['name'],
                    'module' => $permission['module'],
                    'description' => $permission['description'],
                ]
            );
        }

        $roles = [
            'super_admin' => [
                'name' => 'Super Admin',
                'slug' => 'super-admin',
                'description' => 'Full system access',
                'permissions' => Permission::all()->pluck('id')->toArray(), // All permissions
            ],
            'admin' => [
                'name' => 'Admin',
                'slug' => 'admin',
                'description' => 'Administrator access (except super admin)',
                'permissions' => Permission::whereNotIn('slug', ['delete-role', 'assign-role-user'])->pluck('id')->toArray(), // All except role deletion and assignment to prevent locking out
            ],
            'staff' => [
                'name' => 'Staff',
                'slug' => 'staff',
                'description' => 'Staff member with limited access',
                'permissions' => Permission::whereIn('module', ['User Management', 'Reports'])->whereIn('slug', [
                    'view-users', 'view-reports', 'export-reports'
                ])->pluck('id')->toArray(),
            ],
            'customer' => [
                'name' => 'Customer',
                'slug' => 'customer',
                'description' => 'Customer with view-only access to own data',
                'permissions' => [], // Customers typically have no backend access
            ],
        ];

        foreach ($roles as $roleData) {
            $role = Role::firstOrCreate(
                ['slug' => $roleData['slug']],
                [
                    'name' => $roleData['name'],
                    'description' => $roleData['description'],
                ]
            );

            if (!empty($roleData['permissions'])) {
                $role->permissions()->sync($roleData['permissions']);
            }
        }
    }
}
