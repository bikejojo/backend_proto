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

    public function removeRoles($root , array $args){}
}
