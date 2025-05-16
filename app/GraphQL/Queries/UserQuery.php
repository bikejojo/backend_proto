<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Cliente_Interno;
use App\Models\Servicio;
use App\Models\Solicitud;
use App\Models\Tecnico;
use App\Models\User;
use App\Services\StateCatalog;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserQuery{

    protected $active = StateCatalog::STATUS_ACTIVE;
    protected $low = StateCatalog::STATUS_LOW;
    protected $admins = StateCatalog::USER_ADMINS;

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
                    'status' => $user->status,
                    'roles' => $user->roles->map(function ($role) {
                                return ['name' => $role->name]; // Devuelve objetos en lugar de un array plano
                            })->toArray(),
                    'permissions' => $user->permissions->map(fn($perm) => ['name' => $perm->name])->toArray(), // Solo permisos del usuario
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

    public function me($root,array $args){
        $user = Auth::user();
        if(!$user){
            return null;
        }

        return [
            'id' => $user->id,
            'email' => $user->email,
            'status' => $user->status ,
            'role' => $user->roles->first(),
            'permissions' => $user->permissions->map(fn($perm) => [
                'id' => $perm->id, // Agregar el ID del permiso
                'name' => $perm->name
            //'permissions' => $user->permissions->get(),
            ]),
        ];
    }

    public function countParams($root,array $args){
        try {
            $content = [
                'technician' => Tecnico::count(),
                'client' => Cliente_Interno::count(),
                'requests' => Solicitud::count(),
                'service' => Servicio::count()
            ];

            return [
                'message' => 'Conteo exitoso del conteo.',
                'conteo' => $content
            ];
        } catch (\Exception $e) {
            return [
                'message' => 'Error durante el conteo: ' . $e->getMessage(),
                'status' => 'error'
            ];
        }
    }

    public function filterUserEmail($root , array $args){
        try {
            $cont = $args['email'];
            $users = User::where('email','ILIKE','%'.$cont . '%')->get();

            if($users->isEmpty()){
                return [
                    'message' => 'No existen usuarios que coincidan con tu búsqueda.',
                    'user' => null
                ];
            }
            //dd($users);
            $userData = $users->map(function ($user) {
                $role = $user->roles->first();
                return [
                    'id' => $user->id,
                    'email' => $user->email,
                    'ci' => $user->ci,
                    'type_user' => $user->type_user,
                    'status' => $user->status,
                    'roles' => [
                        'name' => optional($role)->name
                    ],
                    'permissions' => $user->permissions
                        ->map(fn($perm) => ['name' => $perm->name])
                        ->toArray(),
                    ];
                });
            //dd($userData);
            return [
                'message' => 'Resultados' ,
                'user' => $userData
            ];

        }catch(\Exception $e){
            Log::info('Se presentaron las siguientes fallas en el user' . $e->getMessage());
            return [
                'message' => 'Se presentaron las fallas ' . $e->getMessage()
            ];
        }
    }
}
