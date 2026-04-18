<?php

namespace Database\Seeders;

use App\Models\Department;
use App\Models\Category;
use App\Models\Permission;
use App\Models\Role;
use App\Models\SystemSetting;
use App\Models\User;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Permissions
        $permissionCodes = [
            'manage_users' => 'Manage Users',
            'manage_roles_permissions' => 'Manage Roles & Permissions',
            'manage_departments' => 'Manage Departments',
            'manage_categories' => 'Manage Categories',
            'manage_inventory' => 'Manage Inventory',
            'archive_inventory' => 'Archive Inventory',
            'adjust_stock' => 'Adjust Stock',
            'issue_items' => 'Issue Items',
            'receive_returns' => 'Receive Returns',
            'manage_repairs' => 'Manage Repairs',
            'manage_custom_fields' => 'Manage Custom Fields',
            'import_csv' => 'Import CSV',
            'export_data' => 'Export Data',
            'view_audit_log' => 'View Audit Log',
            'view_dashboard' => 'View Dashboard',
        ];

        foreach ($permissionCodes as $code => $name) {
            Permission::firstOrCreate(['code' => $code], ['name' => $name]);
        }

        // 2. Create Roles
        $admin = Role::firstOrCreate(['code' => 'admin'], [
            'name' => 'Admin',
            'description' => 'Full system access',
            'is_system' => true,
        ]);

        $storeOfficer = Role::firstOrCreate(['code' => 'store_officer'], [
            'name' => 'Store Officer',
            'description' => 'Operational control of inventory',
            'is_system' => true,
        ]);

        $departmentUser = Role::firstOrCreate(['code' => 'department_user'], [
            'name' => 'Department User',
            'description' => 'Department-scoped access',
            'is_system' => true,
        ]);

        $viewer = Role::firstOrCreate(['code' => 'viewer'], [
            'name' => 'Viewer',
            'description' => 'Read-only access',
            'is_system' => true,
        ]);

        // 3. Assign Permissions to Roles
        $admin->permissions()->sync(Permission::pluck('id'));

        $storeOfficer->permissions()->sync(
            Permission::whereIn('code', [
                'manage_departments', 'manage_categories', 'manage_inventory', 'archive_inventory',
                'adjust_stock', 'issue_items', 'receive_returns',
                'manage_repairs', 'import_csv', 'export_data', 'view_dashboard',
            ])->pluck('id')
        );

        $departmentUser->permissions()->sync(
            Permission::whereIn('code', [
                'view_dashboard', 'export_data',
            ])->pluck('id')
        );

        $viewer->permissions()->sync(
            Permission::whereIn('code', [
                'view_dashboard',
            ])->pluck('id')
        );

        // 4. Create Admin User
        $adminUser = User::firstOrCreate(['email' => 'admin@iit.edu'], [
            'name' => 'System Admin',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $adminUser->roles()->syncWithoutDetaching([$admin->id]);

        // 5. Create sample departments
        $departments = [
            ['name' => 'Administration', 'code' => 'ADMIN', 'description' => 'Administrative department'],
            ['name' => 'IT Department', 'code' => 'IT', 'description' => 'Information Technology'],
            ['name' => 'Finance', 'code' => 'FIN', 'description' => 'Finance department'],
            ['name' => 'Human Resources', 'code' => 'HR', 'description' => 'Human Resources department'],
            ['name' => 'Operations', 'code' => 'OPS', 'description' => 'Operations department'],
        ];

        foreach ($departments as $dept) {
            Department::firstOrCreate(['code' => $dept['code']], $dept);
        }

        // 6. Create sample categories
        $categories = [
            ['name' => 'Electronics', 'code' => 'ELEC', 'description' => 'Electronic equipment'],
            ['name' => 'Furniture', 'code' => 'FURN', 'description' => 'Office furniture'],
            ['name' => 'Stationery', 'code' => 'STAT', 'description' => 'Office stationery and supplies'],
            ['name' => 'Cleaning Supplies', 'code' => 'CLEAN', 'description' => 'Cleaning materials'],
            ['name' => 'Safety Equipment', 'code' => 'SAFE', 'description' => 'Safety and protective equipment'],
        ];

        foreach ($categories as $cat) {
            Category::firstOrCreate(['code' => $cat['code']], $cat);
        }

        // 7. System Settings
        SystemSetting::setValue('inventory.low_stock_default_threshold', '10', 'integer');
        SystemSetting::setValue('app.organization_name', 'IIT', 'string');

        // 8. Create sample users
        $itDept = Department::where('code', 'IT')->first();
        $storeUser = User::firstOrCreate(['email' => 'store@iit.edu'], [
            'name' => 'Store Officer',
            'password' => bcrypt('password'),
            'is_active' => true,
        ]);
        $storeUser->roles()->syncWithoutDetaching([$storeOfficer->id]);

        $deptUser = User::firstOrCreate(['email' => 'user@iit.edu'], [
            'name' => 'Department User',
            'password' => bcrypt('password'),
            'department_id' => $itDept?->id,
            'is_active' => true,
        ]);
        $deptUser->roles()->syncWithoutDetaching([$departmentUser->id]);
    }
}
