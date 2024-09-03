<?php

namespace App\GraphQL\Mutations;
use Illuminate\Support\Facades\Auth;


use App\Models\User;

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
        return $user = User::create($args);
    }

    public function update($root , array $args){
        $id= $args['id'];
        $args['password'] = bcrypt($args['password']);
        $user=User::where('id',$id)->update(['name'=>$args['name'],'email'=>$args['email'],'password'=>$args['password']]);
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

    public function login($root, array $args){
        if(!Auth::attempt(['email' => $args['email'], 'password' => $args['password']])){
            throw new \Exception('Credecincials invalidos');
        }
        $user = Auth::user();
        $token = $user->createToken('Api Tken')->plainTextToken;
        return $token;
    }   
    public function logout(){}

    
}