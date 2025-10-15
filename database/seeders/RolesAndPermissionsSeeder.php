<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Create Roles
        $admin = Role::create([
            'name' => 'admin',
            'display_name' => 'Administrator',
            'description' => 'Full system access'
        ]);

        $security = Role::create([
            'name' => 'security',
            'display_name' => 'Security Officer',
            'description' => 'Visitor and vehicle management'
        ]);

        $hmisStaff = Role::create([
            'name' => 'hmis_staff',
            'display_name' => 'HMIS Staff',
            'description' => 'Hospital management information system access'
        ]);

        $receptionist = Role::create([
            'name' => 'receptionist',
            'display_name' => 'Receptionist',
            'description' => 'Front desk operations'
        ]);

        // Create Permissions
        $permissions = [
            // Visitor Management
            ['name' => 'visitors.view', 'display_name' => 'View Visitors', 'module' => 'visitors'],
            ['name' => 'visitors.create', 'display_name' => 'Register Visitors', 'module' => 'visitors'],
            ['name' => 'visitors.checkout', 'display_name' => 'Checkout Visitors', 'module' => 'visitors'],
            
            // Vehicle Management
            ['name' => 'vehicles.view', 'display_name' => 'View Vehicles', 'module' => 'vehicles'],
            ['name' => 'vehicles.create', 'display_name' => 'Register Vehicles', 'module' => 'vehicles'],
            ['name' => 'vehicles.checkout', 'display_name' => 'Checkout Vehicles', 'module' => 'vehicles'],
            
            // HMIS OPD
            ['name' => 'hmis.opd.view', 'display_name' => 'View OPD Register', 'module' => 'hmis_opd'],
            
            // HMIS Ward
            ['name' => 'hmis.ward.view', 'display_name' => 'View Ward Register', 'module' => 'hmis_ward'],
            
            // HMIS Visitors
            ['name' => 'hmis.visitors.view', 'display_name' => 'View Inpatient Visitors', 'module' => 'hmis_visitors'],
            ['name' => 'hmis.visitors.checkin', 'display_name' => 'Check-in Visitors', 'module' => 'hmis_visitors'],
            ['name' => 'hmis.visitors.checkout', 'display_name' => 'Checkout Visitors', 'module' => 'hmis_visitors'],
            
            // HMIS Vehicles
            ['name' => 'hmis.vehicles.view', 'display_name' => 'View HMIS Vehicles', 'module' => 'hmis_vehicles'],
            ['name' => 'hmis.vehicles.create', 'display_name' => 'Check-in HMIS Vehicles', 'module' => 'hmis_vehicles'],
            
            // HMIS Discharges
            ['name' => 'hmis.discharges.view', 'display_name' => 'View Discharges', 'module' => 'hmis_discharges'],
            
            // Reports
            ['name' => 'reports.view', 'display_name' => 'View Reports', 'module' => 'reports'],
            ['name' => 'reports.export', 'display_name' => 'Export Reports', 'module' => 'reports'],
            
            // User Management
            ['name' => 'users.view', 'display_name' => 'View Users', 'module' => 'users'],
            ['name' => 'users.create', 'display_name' => 'Create Users', 'module' => 'users'],
            ['name' => 'users.edit', 'display_name' => 'Edit Users', 'module' => 'users'],
            ['name' => 'users.delete', 'display_name' => 'Delete Users', 'module' => 'users'],
        ];

        foreach ($permissions as $perm) {
            Permission::create($perm);
        }

        // Assign all permissions to admin
        $admin->permissions()->attach(Permission::all());

        // Assign specific permissions to security
        $security->permissions()->attach(Permission::whereIn('module', ['visitors', 'vehicles'])->get());

        // Assign HMIS permissions to HMIS staff
        $hmisStaff->permissions()->attach(Permission::where('module', 'LIKE', 'hmis%')->get());

        // Assign basic permissions to receptionist
        $receptionist->permissions()->attach(Permission::whereIn('name', [
            'visitors.view', 'visitors.create', 'visitors.checkout',
            'vehicles.view'
        ])->get());
    }
}
