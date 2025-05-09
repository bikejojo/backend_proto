<?php

namespace App\GraphQL\Mutations;

use App\Models\Cliente_Interno;
use App\Models\Tecnico_Habilidad;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use App\Models\Ciudad;
use App\Models\Devices;
use App\Models\DevicesUser;
use App\Models\Tecnico;
use App\Models\User;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\Hash;
use App\Services\StateCatalog;

class UserMutations{

     /**
     * Handle the login mutation.
     *
     * @param  null  $_
     * @param  array  $args
     * @return string
     * @throws ValidationException
     */

    protected $active = StateCatalog::STATUS_ACTIVE;
    protected $low = StateCatalog::STATUS_LOW;
    public function create($root , array $args){
        $userData = $args['userRequest'];
        $email = strtolower(trim($userData['email']));
        //dd(User::where('email',$email)->first()->exists());
        if(User::where('email',$email)->exists()){
            return null;
        }

        $user = new User();
            $user->email = $email;
            $user->password = Hash::make($userData['password']);
            $user->type_user = 3;
            $user->save();
        $tokens = $user->createToken('authToken')->plainTextToken;
        $user->token = $tokens;
        $user->save();
        // Asignar rol usando Spatie
        //
        if (!empty($userData['role'])) {
            $role = Role::whereRaw("name ILIKE ?", ["%{$userData['role']}%"])->first();
            if ($role) {
                $user->assignRole($role);
            }
        }
        return $user;
    }

    public function update($root , array $args){
        $userData = $args['userRequest'];
        $user = User::find($args['id']);
        if(!$user){
            throw new \Exception('Usuario no encontrado');
        }
        //$user->ci=$userData['ci']??$user->ci;
        $user->type_user=$userData['type_user']??$user->tipo_usuario;
        $user->email=$userData['email']??$user->email;
        $user->password=isset($userData['password']) ? Hash::make($userData['password']): $user->contrasenia;
        $user->save();
        return $user;
    }

    public function delete($root,array $args){
        $id= $args['id'];
        if(!$id){
            return ['message'=> 'Borrado no existoso'];
        }else{
            $user=User::where('id',$id)->delete();
            return ['message'=> 'Borrado existoso'];
        }
    }

    public function setUpUser($root,array $args){
        try{
            $user = User::where('id',$args['id_user'])
                        ->where('status',$this->low)
                        ->first();
            //dd($user);
            if(is_null($user)){
                return [
                    'message' => 'Se presento fallas al encontrar el ID del usuario'
                ];
            }
            $user['status'] = $this->active;
            $user->save();

            return [
                'message'=>'Se restablecio con exito al usuario',
                'users'=> $user
            ];

        } catch (\Exception $err) {
            return [
                'message'=>'Se presentaron las siguientes fallas : '. $err->getMessage(),
                'users'=>[]
            ];
        };
    }

    public function anullUser($root,array $args){
        try{
            $user = User::where('id',$args['id_user'])->first();
            if(is_null($user)){
                return [
                    'message' => 'Se presento fallas al encontrar el ID del usuario'
                ];
            }
            if( $user['status'] != $this->low ){
                $user['status'] = $this->low;
                $user->save();
            }else{
                return [
                    'message' => 'El usuario esta inhabilitado'
                ];
            }
            return [
                'message'=>'Se dio de baja con exito al usuario',
                'users'=> $user
            ];

        } catch (\Exception $err) {
            return [
                'message'=>'Se presentaron las siguientes fallas : '.$err->getMessage(),
                'users'=>[]
            ];
        };
    }

    public function login($root, array $args)
    {
        //verificar si el CI se encuentra registrado en la dupla
        $user = User::where('ci', $args['ci'])->first();

        if ($user == null ) {
            return [
                'message' => "El usuario con CI no existe",
                'user' => null,
                'tecnico' => null,
                'skill' => null
            ];
        }
        // Error en la contraseña
        if (!Hash::check($args['password'], $user->password)) {
            Log::warning('Intento de login fallido: Contraseña incorrecta.', ['ci' => $args['ci']]);
            return [
                'message' => "Credenciales invalidas" ,
            ];
        }


        $tecnico = $user->technicians()->first();
        //status tecnico

        if($tecnico !== null ){
            $habilidades_tec = Tecnico_Habilidad::where('technicianId', $tecnico->id)
            ->get();
            $ha=[];
            foreach($habilidades_tec as $habilidad_tec) {
                if ($habilidad_tec->skill) {
                    // Añadimos los detalles de la habilidad al array
                    $ha[] = [
                        'id_skill' => $habilidad_tec->skill->id,
                        'name' => $habilidad_tec->skill->name,
                        'experience' => $habilidad_tec->experience,
                    ];
                }
            }
        }
   // Crear un token con Sanctum
        $tokens = $user->createToken('authToken')->plainTextToken;
        $user->token = $tokens;
        $user->save();
        if($tecnico !== null ){
            if($ha !== null ){
                return [
                    'message' => 'Login exitoso',
                    'user' => $user,
                    'technician' => $tecnico,
                    'skills' => $ha
                ];
            }else{
                return [
                    'message' => 'Login exitoso',
                    'user' => $user,
                    'technician' => $tecnico,
                    'skills' => null
                ];
            }
        }else{
            return [
                'message' => 'Login exitoso',
                'user' => $user,
                'skills' => null
            ];
        }

        return [
            'message' => 'Ups! sucedio un problema en este endpoint.'
        ];
    }

    public function loginClient($root, array $args)
    {
        $user = User::where('email', $args['email'])->first();

        if ($user == null) {
            return [
                'message' => "El usuario con el correo no existe",
                'status' => 2
            ];
        }

        if (!Hash::check($args['password'], $user->password)) {
            return [
                'message' => "Credenciales inválidas",
                'status' => 2
            ];
        }


        // Obtener cliente asociado
        $client = $user->clientsExterns()->first();

        $client1 =Cliente_Interno::where('internal_clients.userId',$user->id)->first();
        $ciudad = Ciudad::find($client1->cityId);
        // Crear token con Sanctum
        $tokens = $user->createToken('authToken')->plainTextToken;
        $user->token = $tokens;
        $user->save();

        // Retornar respuesta
        if ($client !== null) {
            return [
                'message' => 'Login exitoso',
                'user' => $user,
                'client' => $client1,
                'city' => [
                        'id_city' => $ciudad->id,
                        'name' => $ciudad->name,
                ],
                'status' => 1
            ];
        } else {
            return [
                'message' => 'El usuario no cuenta con una cuenta activa.',
                'user' => $user,
                'client' => null,
                'status' => 2
            ];
        }
    }

    public function loginAdmin($root,array $args){
        // Verificar si el CI se encuentra registrado
        $user = User::where('email', $args['email'])->first();

        if ($user == null) {
            return [
                'message' => "El usuario con el correo no existe"
            ];
        }

        // Verificar contraseña
        if (!Hash::check($args['password'], $user->password)) {
            return [
                'message' => "Credenciales inválidas"
            ];
        }
        if(!($user->type_user === 3)){
            return [
                'message' => 'El usuario no es el permitido.'
            ];
        }

        $tokens = $user->createToken('authToken')->plainTextToken;
        $user->token = $tokens;
        $user->save();
        $role=$user->roles->first();
        $permissions = $user->permissions->map(function ($permission) {
            return [
                'id' => $permission->id,
                'name' => $permission->name
            ];
        });

        if ($user !== null) {
            return [
                'message' => 'Login exitoso',
                'user' => $user,
                'role' => $role,
                'permissions'  => $permissions,
            ];
        } else {
            return [
                'message' => 'El usuario no cuenta con una cuenta.',
                'user' => $user
            ];
        }

    }

    public function logout($root, array $args)
    {
        $user = Auth::user(); // Obtener el usuario autenticado

        if ($user) {
            // Obtén el token actual del usuario y elimínalo
            $currentToken = $user->currentAccessToken();

            if ($currentToken) {
                $currentToken->delete(); // Eliminar el token actual
                $user->update(['token' => null]);
                return [
                    'message' => 'Logout exitoso'
                ];
            }

            $deviceUser = DevicesUser::where('users_id',$user->id)->first();

            $device = Devices::where('id',$deviceUser->device_id)->first();
            $deviceUser->delete();
            $device->delete();
            return [
                'message' => 'No se encontró el token actual'
            ];
        }

        return [
            'message' => 'No se encuentra usuario'
        ];
    }

}
