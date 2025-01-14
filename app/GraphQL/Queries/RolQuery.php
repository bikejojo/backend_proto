<?php

namespace App\GraphQL\Queries;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Models\Permission;

class RolQuery
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function getRol(){
        $role = Role::all();
        return [
            'message' => 'Listado de roles.',
            'rol' => $role
        ];
    }

    public function getPermisse($root, array $args){
        try {
            $name_role = $args['name'];
            $id_role = Role::where('name',$name_role)->first();
            $permissions = $id_role->permissions;
            //dd($permissions);
            return [
                'message' => 'Listado de permisos para el rol',
                'permissions' => $permissions
            ];
        } catch (\Exception $e){
            return [
                'message' => 'Surgio un problema en la base de datos.' . $e->getMessage()
            ];
        }
    }
}
