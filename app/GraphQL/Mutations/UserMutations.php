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
        
        $token = $user->createToken('token')->plainTextToken;

        return [$user,$token];
    }

    public function update($root , array $args){
        $id= $args['id'];
        $args['password'] = bcrypt($args['password']);
        $user=User::where('id',$id)->update(['ci'=>$args['ci'],'email'=>$args['email'],'password'=>$args['password']]);
        return User::find($id);
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