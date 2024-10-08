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
        $user = User::create([
            'email' => $userData['email'],
            'contrasenia' => Hash::make($userData['contrasenia']),
            'ci' => $userData['ci'],
            'tipo_usuario' =>$userData['tipo_usuario'],
        ]);

        //$token= $user->createToken('token'.$user['ci'])->plainTextToken;
        //$user->token = $token;
        $user->save();

        return $user;
    }

    public function update($root , array $args){
        $userData = $args['userRequest'];
        $user = User::find($args['id']);
        if(!$user){
            throw new \Exception('Usuario no encontrado');
        }
        $user->ci=$userData['ci']??$user->ci;
        $user->tipo_usuario=$userData['tipo_usuario']??$user->tipo_usuario;
        $user->email=$userData['email']??$user->email;
        $user->contrasenia=isset($userData['contrasenia']) ? Hash::make($userData['contrasenia']): $user->contrasenia;
        $user->save();
        return $user;
    }

    public function delete($root,array $args){
        $id= $args['id'];
        if($id){
            return ['message'=> 'Borrado no existoso'];
        }else{
            $user=User::where('id',$id)->delete();
            return ['message'=> 'Borrado existoso'];
        }
    }

    public function login($root, array $args)
    {
        $user = User::where('ci', $args['ci'])->first();
        if (!$user) {
            return [
                'message' => "El usuario con CI no existe",
                'user' => null,
                'tecnico' => null
            ];
        }
        $tecnico = $user->tecnicos()->first();
        if (!$user || !Hash::check($args['contrasenia'], $user->contrasenia)) {
            return [
                'message' => "No existe Usuario y/o contrasenia invalida" ,
                'user' => null,
                'tecnico' => null

            ];
        }

   // Crear un token con Sanctum
        $tokens = $user->createToken('authToken')->plainTextToken;
        $user->token = $tokens;

        return [
            'message' => 'Login exitoso',
            'user' => $user,
            'tecnico' => $tecnico
        ];
    }

}
