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

    public function updateRoles($root,array $args){}

    public function removeRoles($root , array $args){}
}
