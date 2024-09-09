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
            'ci' => $userData['ci'],
            'tipo_usuario' => $userData['tipo_usuario'],
            'email' => $userData['email'],
            'password' => Hash::make($userData['password']),
        ]);
<<<<<<< HEAD
        
        $token = $user->createToken('token'+$user['ci'])->plainTextToken;
=======

        $token = $user->createToken('token')->plainTextToken;
>>>>>>> b5ca0d2b462be5ba851b44fba2ced57bed1a8cf3

        //Log::info('Usuario creado con éxito, ID:', $user->id);

        return [$user,$token];
    }

    public function update($root , array $args){
        /*$id= $args['id'];
        $args['password'] = bcrypt($args['password']);
        $user=User::where('id',$id)->update(['ci'=>$args['ci'],'email'=>$args['email'],'password'=>$args['password']]);
        return User::find($id);*/
        $user = User::find($args['id']);
        if(!$user){
            throw new \Exception('Usuario no encontrado');
        }
        $user->ci=$args['userRequest']['ci'];
        $user->tipo_usuario=$args['userRequest']['tipo_usuario'];
        $user->email=$args['userRequest']['email'];
        $user->password=Hash::make($args['userRequest']['password']);
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
<<<<<<< HEAD
    
            
        return $user;
    } 
=======

        $token = $user->createToken('Api Token')->plainTextToken;

        return $token;
    }
>>>>>>> b5ca0d2b462be5ba851b44fba2ced57bed1a8cf3
    public function logout(){}
}
