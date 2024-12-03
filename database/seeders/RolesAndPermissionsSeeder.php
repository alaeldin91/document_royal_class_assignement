<?php
namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Role;
use App\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    public function run()
    {
        // Create roles
        $admin = Role::create(['name' => 'admin']);
        $user = Role::create(['name' => 'user']);
        $viewer = Role::create(['name' => 'viewer']);

        // Create permissions
        $deleteDocumentPermission = Permission::create(['name' => 'delete document']);
        $viewDocumentPermission = Permission::create(['name' => 'view document']);

        // Assign permissions to roles
        $admin->givePermissionTo($deleteDocumentPermission);
        $admin->givePermissionTo($viewDocumentPermission);
        $user->givePermissionTo($viewDocumentPermission);

    }

}
