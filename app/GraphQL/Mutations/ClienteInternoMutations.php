<?php

namespace App\GraphQL\Mutations;


use App\Models\User;
use App\Models\Cliente_Interno;
use App\Services\StateCatalog;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\DB;

class ClienteInternoMutations{
    public function create($root, array $args){
    //dd($args['clientRequest']);
        $clienteData = $args['clientRequest'];
        // Crear el cliente en la base de datos
        if (User::where('ci',$clienteData['ci'])->exists()) {
            return [
                 'message'=> 'Esta celula de identidad ya esta en uso, por favor intenta con otro.'
            ];
         }
        if (strlen($clienteData['ci']) != 7) {
            return [
                'message' => 'Tu CI debe tener 7 digitos!',
            ];
        }
        $validators = $this->validateImage($args);
        if ($validators->fails()) {
            return [
                'message' => 'Archivo de imagen inválido.',
                'upcomingmessage' => 'Registre su usuario'
            ];
        }
        DB::beginTransaction();
        try{
        $email = strtolower(trim($clienteData['email']));
        $user = User::create([
            'email' => $email,
            'password' => Hash::make($clienteData['password']),
            'ci' => $clienteData['ci'],
            'type_user' => StateCatalog::USER_CLIENT,
        ]);
        $tokens = $user->createToken('authToken')->plainTextToken;
        $user->token = $tokens;
        $user->save();
        $userId = $user->id;
        $clienteData['userId'] = $userId;
        $cliente = Cliente_Interno::create($clienteData);
        $clientId = $cliente->id;

        $manager = new ImageManager(new Driver());
            // Manejo de la imagen del cliente (si se envió una)
        if (isset($args['photo']) && $args['photo'] instanceof UploadedFile) {
            $fotoPath = $this->processImage($args['photo'], "/{$clientId}_client/foto.png",$manager);
            $cliente->photo = str_replace('public/', '', $fotoPath);  // Guardar la ruta de la imagen
        }
        $cliente->save();
        $cliente=Cliente_Interno::find($clientId);
        DB::commit();
        return [
            'message' => 'Creacion Cliente exitoso!',
            'client' => $cliente,
            'user' => $user
        ];
        DB::rollBack();
    }catch (\Exception $e){
        return ['message' => 'El error es.'. $e->getMessage()];
    }
    }
    public function update($root ,array $args){
        $clientData = $args['clientRequest'];
        $client = Cliente_Interno::find($args['id']);
        //dd($client);
        $clientId = $client->id;
        $user = User::find($client->userId);
        //dd($user);
        if ($client==null){
            throw new \Exception('Client not found.');
        }
        $firstName = trim($clientData['firstName']);

        $lastName = trim($clientData['lastName']);
        $email = trim($clientData['email']);
        $phone = trim($clientData['phoneNumber']);
        $client->firstName=$firstName;
        $client->lastName=$lastName;
        $client->email=$email;
        $client->phoneNumber=$phone;
        $client->loginMethod=$clientData['loginMethod'];
        $client->cityId = $clientData['cityId'];

        $manager = new ImageManager(new Driver());
        if (isset($args['photo']) && $args['photo'] instanceof UploadedFile) {
            //dd($technician->photo);
            if ($client->photo) {
                Storage::delete('public/' . $client->photo);
            }
            $photoPath = $this->processImage($args['photo'], "/{$clientId}_client/foto.png",$manager);
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
        $id=Cliente_Interno::find($args['id']);
        //dd($id);
        if(!$id){
            return ['message'=> 'Borrado no existoso'];
        }else{
            $id->delete();
            return ['message'=> 'Borrado existoso'];
        }
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

    // Validación de imágenes
    private function validateImage($args){
        return Validator::make([
        'photo' => $args['photo'] ?? null ,
        ], [

            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
        ]);
    }
}
