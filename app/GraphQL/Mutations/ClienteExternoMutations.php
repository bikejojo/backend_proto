<?php

namespace App\GraphQL\Mutations;


use App\Models\User;
use App\Models\Cliente_Externo;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;

class ClienteExternoMutations{
    public function create($root, array $args){
    //dd($args['clientRequest']);
        $clienteData = $args['clientRequest'];
        // Crear el cliente en la base de datos

        $cliente = Cliente_Externo::create([
            'firstName' => $clienteData['firstName'],
            'lastName' => $clienteData['lastName'],
            'email' => strtolower(trim($clienteData['email'])),  // Convertir email a minúsculas y eliminar espacios
            'loginMethod' => $clienteData['loginMethod'] ?? null, // Campo opcional
            'photo' => $clienteData['photo'] ?? null, // Campo opcional
            'userId' => $clienteData['userId'],
            'cityId' => $clienteData['cityId'],
        ]);
        $clientId = $cliente->id;
        //dd($clientId);
        $this->createTechnicianDirectories($clientId);
        $manager = new ImageManager(new Driver());
            // Manejo de la imagen del cliente (si se envió una)
        if (isset($args['photo']) && $args['photo'] instanceof UploadedFile) {
            $fotoPath = $this->processImage($args['photo'], "/{$clientId}_client/foto.png",$manager);
            $cliente->photo = str_replace('public/', '', $fotoPath);  // Guardar la ruta de la imagen
        }
        $cliente->save();
        // Retornar el cliente recién creado
        #return $cliente;
        return [
            'message' => 'Creacion Cliente exitoso!',
            'client' => $cliente
        ];
    }
    public function update($root ,array $args){
        $clientData = $args['clientRequest'];
        $client = Cliente_Externo::find($args['id']);
        $clientId = $client->id;
        $user = User::find($client['userId']);
        /*$cliente = Cliente_Externo::where('id',$id)
        ->update(['nombre'=>$args['nombre'],'email'=>$args['email'],'metodo_login'=>$args['metodo_login'],'foto'=>$args['foto'],'users_id'=>$args['users_id']]);*/
        //return Cliente_Externo::find($id);
        if ($client==null){
            throw new \Exception('Client not found.');
        }
        $firstName = trim($client['firstName']);
        $lastName = trim($client['lastName']);
        $email = trim($client['email']);
        $client->firstName=$firstName;
        $client->lastName=$lastName;
        $client->email=$email;
        $client->loginMethod=$clientData['loginMethod'];
        $client->userId = $clientData['userId'];
        $client->cityId = $clientData['cityId'];
        $manager = new ImageManager(new Driver());
        if (isset($args['photo']) && $args['photo'] instanceof UploadedFile) {
            //dd($technician->photo);
            if ($client->photo) {
                Storage::delete('public/' . $client->photo);
            }
            $photoPath = $this->processImage($args['photo'], "$clientId/profile/photo.png", $manager);
            $client->photo = str_replace('public/', '', $photoPath);
        }
        $client->save();
        $user->email = $email ?? $user->email;
        $user->save();

        return[
            'message' => 'Cliente actualizado exitoso!!' ,
            'client' => $client
        ];
    }
    public function delete($root ,array $args){
            $id=Cliente_Externo::find($args['id']);
            if($id){
                return ['message'=> 'Borrado no existoso'];
            }else{
                $user=User::where('id',$id)->delete();
                return ['message'=> 'Borrado existoso'];
            }
    }

    private function createTechnicianDirectories($clientId){
        Storage::makeDirectory('public/' . $clientId.'_client' . '/photo');
    }
    // Procesamiento de imágenes
    private function processImage(UploadedFile $file, $path, $manager){
        $image = $manager->read($file->getRealPath());
        $image->resize(750, 750, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $fullPath = storage_path("app/public/{$path}");
        $image->save($fullPath, 80, 'png');
        return $path;
    }
}
