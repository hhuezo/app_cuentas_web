<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use App\Models\User;

class RolesAndPermissionsSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        app()[\Spatie\Permission\PermissionRegistrar::class]->forgetCachedPermissions();

        Permission::create( ['name' => 'create roles'] );
        Permission::create( ['name' => 'read roles'] );
        Permission::create( ['name' => 'edit roles'] );
        Permission::create( ['name' => 'delete roles'] );

        Permission::create( ['name' => 'create users'] );
        Permission::create( ['name' => 'read users'] );
        Permission::create( ['name' => 'edit users'] );
        Permission::create( ['name' => 'delete users'] );

        Permission::create( ['name' => 'create permissions'] );
        Permission::create( ['name' => 'read permissions'] );
        Permission::create( ['name' => 'edit permissions'] );
        Permission::create( ['name' => 'delete permissions'] );


        $role = Role::create( ['name' => 'administrador'] );
        $role = Role::findOrFail(1);
        $role->givePermissionTo( Permission::all() );

        // Crear usuario administrador
        $adminUser = User::create([
            'name' => 'Administrador',
            'email' => 'admin@example.com',
            'username' => 'admin', // Asegúrate de incluir el campo si lo usas
            'password' => bcrypt('password'), // Cambia 'password' por una contraseña real
        ]);

        // Asignar el rol de administrador al usuario creado
        $adminUser->assignRole($role);
    }
}
