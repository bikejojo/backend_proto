<?php

namespace App\GraphQL\Mutations;
use Illuminate\Support\Facades\Auth;


use App\Models\User;
use Illuminate\Support\Facades\Hash;


class UserMutations{

     /**
     * Handle the login mutation.
     *
     * @param  null  $_
     * @param  array  $args
     * @return string
     * @throws ValidationException
     */
    public function create($root , array $args){
        $userData = $args['userRequest'];
        $email = strtolower(trim($userData['email']));
        if (strlen($userData['ci']) != 7) {
            throw new \Exception('El CI debe tener exactamente 7 dígitos.');
        }
        $user = User::create([
            'email' => $email,
            'password' => Hash::make($userData['password']),
            'ci' => $userData['ci'],
            'type_user' =>$userData['type_user'],
        ]);
        $tokens = $user->createToken('authToken')->plainTextToken;
        $user->token = $tokens;
        $user->save();
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

    public function login($root, array $args)
    {
        $user = User::where('ci', $args['ci'])->first();

        if ($user == null ) {
            return [
                'message' => "El usuario con CI no existe",
                'user' => null,
                'tecnico' => null
            ];
        }

        $tecnico = $user->technicians()->first();
        if (!Hash::check($args['password'], $user->password)) {
            return [
                'message' => "Credenciales invalidas" ,
            ];
        }

   // Crear un token con Sanctum
        $tokens = $user->createToken('authToken')->plainTextToken;
        $user->token = $tokens;
        $user->save();
        return [
            'message' => 'Login exitoso',
            'user' => $user,
            'technician' => $tecnico
        ];
    }

   /* public function logout($root, array $args)
    {
        $user = Auth::user(); // Obtener el usuario autenticado

        if ($user) {
            // Obtén el token actual del usuario y elimínalo
            $currentToken = $user->currentAccessToken();

            if ($currentToken) {
                $currentToken->delete(); // Eliminar el token actual
                $user->token = null;
                $user->save();
                return [
                    'message' => 'Logout exitoso'
                ];
            }

            return [
                'message' => 'No se encontró el token actual'
            ];
        }

        return [
            'message' => 'No se encuentra usuario'
        ];
    }*/

}
