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
        /*if(!auth::user()->can('assign-roles')){
            return [
                'message' => 'Este usuario no tiene permisos para asignar roles.'
            ];
        }*/
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

    public function assignPermise($root, array $args)
    {
        try{
            $rolData = $args['permiseRequest'];
            $userId = $rolData['userId'];
            $permissions = $rolData['permissions']; // Array de permisos enviado en la solicitud

            // Validar que el usuario existe
            $user = User::find($userId);
            if (!$user) {
                return [
                    'message' => 'Usuario no encontrado.',
                ];
            }

            // Validar que los permisos existen en la base de datos
            $validPermissions = Permission::whereIn('name', $permissions)->get();
            if ($validPermissions->isEmpty()) {
                return [
                    'message' => 'No se encontraron permisos válidos en la solicitud.',
                ];
            }

            // Obtener permisos de roles ya asignados al usuario
            $roles = $user->roles; // Puede ser null si el usuario no tiene roles asignados
            if (!$roles || $roles->isEmpty()) {
                $rolePermissions = collect(); // Crear una colección vacía si no hay roles
            } else {
                $rolePermissions = $roles->flatMap(function ($role) {
                    return $role->permissions->pluck('name');
                })->unique();
            }

            // Filtrar los permisos que no están en los roles del usuario
            $permissionsToAssign = $validPermissions->filter(function ($permissions)use ($rolePermissions){
                return !$rolePermissions->contains($permissions->name);
            });
            if ($permissionsToAssign->isEmpty()) {
                return [
                    'message' => 'Todos los permisos ya están asociados a roles del usuario.',
                ];
            }

            // Asignar los permisos restantes al usuario
            $user->givePermissionTo($permissionsToAssign->pluck('name')->toArray());

            // Opcional: Obtener los permisos asignados al usuario para devolver como respuesta
            $assignedPermissions = $permissionsToAssign->map(function ($permissions){
                return [
                    'id'=>$permissions->id,
                    'name'=>$permissions->name
                ];
            });

            return [
                'message' => 'Permisos asignados exitosamente.',
                'user' => $user ,
                'permissions' => $assignedPermissions,
            ];
        } catch (\Exception $e) {
            return [
                'message' => 'Error en la conexion en la base de datos' . $e->getMessage()
            ];
        }
    }

}
