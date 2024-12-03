<?php

namespace App\GraphQL\Mutations;

use App\Models\Tecnico_Habilidad;
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
        $client = $user->clientsExterns()->first();
        if (!Hash::check($args['password'], $user->password)) {
            return [
                'message' => "Credenciales invalidas" ,
            ];
        }
        $tecnico = $user->technicians()->first();
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
    }

    public function loginClient($root, array $args)
    {
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

        // Obtener cliente asociado
        $client = $user->clientsExterns()->first();

        // Crear token con Sanctum
        $tokens = $user->createToken('authToken')->plainTextToken;
        $user->token = $tokens;
        $user->save();

        // Retornar respuesta
        if ($client !== null) {
            return [
                'message' => 'Login exitoso',
                'user' => $user,
                'client' => $client
            ];
        } else {
            return [
                'message' => 'El usuario no tiene un cliente asociado',
                'user' => $user,
                'client' => null
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
    }

}
