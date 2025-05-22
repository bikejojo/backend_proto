<?php

namespace App\GraphQL\Mutations;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class RolMutations
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function assignRol($root , array $args){

        $userId= User::findOrFail($args['userId']);
        $role = Role::where('name',$args['role'])->first();
        if(!$role){
            return [
                'message' => 'el rol no existe'
            ];
        }
        $userId->assignRole($role);

        return [
            'message' => 'Se asigno un rol al usuario' ,
            'rol' => $role ,
            'user' => $userId
        ];
    }

    public function updateRoles($root,array $args){
        $userData = $args['rolRequest'];
        $userId=$userData['id_user'];
        $nameRol = $userData['name'];
        $user = User::find($userId);
        $user->syncRoles([$nameRol]);
        return [
            'message' => 'Se actualizo el rol del usuario',
            'rol'=> $userId->getRoleNames(),
            'user' => $userId
        ];
    }

    public function assignPermise($root, array $args){

        try {
            if (!isset($args['permiseRequest']['userId']) || !isset($args['permiseRequest']['permissions'])) {
                return [
                    'message' => 'Faltan datos en la solicitud.',
                    'status' => false
                ];
            }

            $userId = $args['permiseRequest']['userId'];

            $permissions = $args['permiseRequest']['permissions']; // Lista de permisos a asignar

            // Buscar usuario con roles y permisos
            $user = User::with('roles.permissions')->find($userId);

            if (!$user) {
                return [
                    'message' => 'Usuario no encontrado.',
                    'status' => false
                ];
            }

            // Obtener todos los permisos de los roles del usuario
            $rolePermissions = $user->roles->flatMap(function ($role) {
                return $role->permissions;
            })->pluck('name')->unique();

            // Obtener permisos que existen en la base de datos
            $validPermissions = Permission::whereIn('name', $permissions)->pluck('name')->toArray();

            if (empty($validPermissions)) {
                return [
                    'message' => 'No se encontraron permisos válidos.',
                    'status' => false
                ];
            }

            // **Validar que los permisos asignados correspondan a los roles del usuario**
            $permissionsToAssign = array_filter($validPermissions, function ($perm) use ($rolePermissions) {
                return $rolePermissions->contains($perm);
            });

            /*if (empty($permissionsToAssign)) {
                return [
                    'message' => 'No tienes roles que permitan asignar estos permisos.',
                    'status' => false
                ];
            }*/

            // **Actualizar permisos dinámicamente**
            $user->syncPermissions($permissionsToAssign); // Quita permisos antiguos y asigna los nuevos

            return [
                'message' => 'Permisos actualizados correctamente.',
                'status' => true,
                'user' => $user,
                'permissions' => $permissionsToAssign
            ];
        } catch (\Exception $e) {
            //Log::error("Error actualizando permisos: " . $e->getMessage());
            return [
                'message' => 'Error en la actualización de permisos.' . $e->getMessage(),
                'status' => false
            ];
        }
    }


}
