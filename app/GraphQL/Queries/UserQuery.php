<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\User;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserQuery{

    protected $client = 2;
    protected $technician = 1;
    protected $admins = 3;

    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function usuarioTecnico($root , array $args){
        return User::with('tecnicos')->findOrFail($args['id']);
    }

    public function getUserRol($root,array $args){
        try{
            $userList = User::where('type_user',$this->admins)
            ->with(['roles:id,name', 'permissions:id,name'])
            ->get();
           // dd($userList);

            if($userList->isEmpty()){
                return [
                    'message'=>'No existe datos en la consulta',
                    'succes'=>'1',
                    'user'=>[],
                ];
            }

            $formattedUsers = $userList->map(function ($user) {
                //dd($user->getAllPermissions());
                return [
                    'id' => $user->id,
                    'name' => $user->name,
                    'email' => $user->email,
                    'roles' => $user->roles->map(function ($role) {
                                return ['name' => $role->name]; // Devuelve objetos en lugar de un array plano
                            })->toArray(),
                    'permissions' => $user->getAllPermissions()->map(fn($perm) => ['name' => $perm->name])->toArray(),
                ];
            });
            //dd($formattedUsers);
            return [
                'message'=>'listado de usuario',
                'succes'=>'2',
                'user'=>$formattedUsers,
            ];

        } catch (\Exception $e){
            return [
                'message'=>'Fallas en la siguiente parte :' . $e->getMessage(),
                'succes'=>'3',
                'user'=>null,
            ];
        }
    }

}
