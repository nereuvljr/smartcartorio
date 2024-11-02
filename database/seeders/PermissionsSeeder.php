<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Models\User;

class PermissionsSeeder extends Seeder
{
    public function run(): void
    {
        // Criar permissões por módulo
        $permissions = [
            // Usuários
            'view_users',
            'create_users',
            'edit_users',
            'delete_users',

            // SmartExtract
            'view_documents',
            'upload_documents',
            'process_documents',
            'validate_documents',

            // SmartDocs
            'view_files',
            'manage_files',
            'approve_files',
            'archive_files',

            // SmartProperties
            'view_properties',
            'create_properties',
            'edit_properties',
            'delete_properties',

            // SmartAlert
            'view_alerts',
            'create_alerts',
            'manage_alerts',
            'send_notifications'
        ];

        foreach ($permissions as $permission) {
            Permission::create(['name' => $permission]);
        }

        // Criar roles
        $admin = Role::create(['name' => 'admin']);
        $manager = Role::create(['name' => 'manager']);
        $user = Role::create(['name' => 'user']);

        // Dar todas as permissões para admin
        $admin->givePermissionTo(Permission::all());

        // Permissões para manager
        $manager->givePermissionTo([
            'view_users',
            'view_documents',
            'upload_documents',
            'process_documents',
            'validate_documents',
            'view_files',
            'manage_files',
            'approve_files',
            'view_properties',
            'create_properties',
            'edit_properties',
            'view_alerts',
            'create_alerts',
            'manage_alerts',
            'send_notifications'
        ]);

        // Permissões básicas para user
        $user->givePermissionTo([
            'view_documents',
            'upload_documents',
            'view_files',
            'view_properties',
            'view_alerts'
        ]);

        // Criar um usuário admin inicial
        $adminUser = User::create([
            'name' => 'Administrador',
            'email' => 'admin@smartcartorio.digital',
            'password' => bcrypt('password'),
            'is_active' => true
        ]);

        $adminUser->assignRole('admin');
    }
}
