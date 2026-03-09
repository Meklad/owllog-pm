<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class RoleAndPermissionSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Reset cached roles and permissions
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        // Create roles
        $superAdmin = Role::create(['name' => 'super-admin']);
        $admin = Role::create(['name' => 'admin']);
        $member = Role::create(['name' => 'member']);

        // Create permissions for each resource
        $resources = ['owners', 'companies', 'projects', 'tasks', 'comments', 'attachments'];

        foreach ($resources as $resource) {
            Permission::create(['name' => "view_{$resource}"]);
            Permission::create(['name' => "create_{$resource}"]);
            Permission::create(['name' => "update_{$resource}"]);
            Permission::create(['name' => "delete_{$resource}"]);
        }

        // View-only permission for status_logs
        Permission::create(['name' => 'view_status_logs']);

        // Assign all permissions to super-admin
        $superAdmin->givePermissionTo(Permission::all());

        // Assign permissions to admin (all CRUD for their own resources)
        $admin->givePermissionTo([
            'view_owners', 'create_owners', 'update_owners', 'delete_owners',
            'view_companies', 'create_companies', 'update_companies', 'delete_companies',
            'view_projects', 'create_projects', 'update_projects', 'delete_projects',
            'view_tasks', 'create_tasks', 'update_tasks', 'delete_tasks',
            'view_comments', 'create_comments', 'delete_comments',
            'view_attachments', 'create_attachments', 'delete_attachments',
            'view_status_logs',
        ]);

        // Assign permissions to member (view/create/update only, no delete)
        $member->givePermissionTo([
            'view_projects', 'create_projects', 'update_projects',
            'view_tasks', 'create_tasks', 'update_tasks',
            'view_comments', 'create_comments', 'delete_comments',
            'view_attachments', 'create_attachments', 'delete_attachments',
            'view_status_logs',
        ]);
    }
}
