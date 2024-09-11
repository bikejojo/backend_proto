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

       /* $userData = $args['userRequest'];
        $user = User::create([
            'ci' => $userData['ci'],
            'tipo_usuario' => $userData['tipo_usuario'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
        ]);

        $token = $user->createToken('token_' . $user['ci'])->plainTextToken;
        $user->token = $token;
        $user->save();

        //Log::info('Usuario creado con éxito, ID:', $user->id);

        return $user;*/
        $userData = $args['userRequest'];
        $email = $userData['email'] ?? null;
        $password = $userData['password'] ?? null;
        $ci=$userData['ci'] ?? null;
        $tipo_usuario=$userData['tipo_usuario'] ?? null;

        if($email && $password && $ci && $tipo_usuario){
            $user = User::create([
                'email' => $email,
                'password' => Hash::make($password),
                'ci' => $ci,
                'tipo_usuario' => $tipo_usuario,
            ]);
            $token = $user->createToken('token_' . $user['ci'])->plainTextToken;
            $user->token = $token;
            $user->save();

            return $user;
        }
        return null;
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
        $user->password=isset($userData['password']) ? Hash::make($userData['password']): $user->password;
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

    public function login($root, array $args) {
        $user = User::where('ci', $args['ci'])->first();

        if (!$user || !Hash::check($args['password'], $user->password)) {
            throw new \Exception('Credenciales inválidas');
        }

        $token = $user->createToken('Api Token')->plainTextToken;

        return $token;
    }

    public function logout(){}
}
