<?php

namespace App\GraphQL\Mutations;

use Carbon\Carbon;
use App\Models\User;
use App\Models\Ciudad;
use App\Helpers\ImageHelper;
use App\Services\StateCatalog;
use App\Events\PasswordChanged;
use App\Models\Cliente_Interno;
use Illuminate\Http\UploadedFile;
use App\Services\ValidationModels;
use Illuminate\Support\Facades\DB;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;

class ClienteInternoMutations{
    //variables
    protected $app;
    protected $now;

    public function __construct() {
        $this->app = env('APP_URL');
        $this->now= Carbon::now()->format('Ymd_His');
    }

    public function create($root, array $args){
        $clienteData = $args['clientRequest'];
        // Crear el cliente en la base de datos
        if (User::where('email',$clienteData['email'])->exists()) {
            return [
                 'message'=> 'Este email ya esta en uso, por favor intenta con otro.',
                 'status' => 2
            ];
         }

        $validators = $this->validateImage($args);

        if ($validators->fails()) {
            return [
                'message' => 'Archivo de imagen inválido.',
                'upcomingmessage' => 'Registre su usuario',
                'status' => 2
            ];
        }
        DB::beginTransaction();
        try{
        $email = strtolower(trim($clienteData['email']));

        $user = new User();
            $user->email = $email;
            $user->password = Hash::make($clienteData['password']);
            $user->type_user = StateCatalog::USER_CLIENT;
            $user->save();
        $tokens = $user->createToken('authToken')->plainTextToken;
        $user->token = $tokens;
        $user->save();

        $userId = $user->id;
        $clienteData['userId'] = $userId;
        $loginMethod=$clienteData['loginMethod'];
        $clienteData['loginMethod'] = $this->methodLogin($loginMethod);
        $clienteData['status'] = 1;

        $cliente = new Cliente_Interno();
            $cliente->firstName = $clienteData['firstName'];
            $cliente->lastName = $clienteData['lastName'];
            $cliente->email = $clienteData['email'];
            $cliente->phoneNumber = $clienteData['phoneNumber'];
            $cliente->cityId = $clienteData['cityId'];
            $cliente->loginMethod = $clienteData['loginMethod'];
            $cliente->userId = $clienteData['userId'];
            $cliente->status = $clienteData['status'];
            $cliente->save();
        $clientId = $cliente->id;
        $value=$user->type_user;

        ImageHelper::createDirectorie($clientId,$value);
        $manager = new ImageManager(new Driver());
        if (isset($args['photo']) && $args['photo'] instanceof UploadedFile) {
            $fotoPath = $this->processImage($args['photo'], "/client_{$clientId}/photo/{$this->now}.png",$manager);
            $cliente->photo = env('APP_URL') . '/storage' . str_replace('public/', '', $fotoPath);  // Guardar la ruta de la imagen
            $cliente->save();
        }

        //$cliente=Cliente_Interno::find($clientId);
        $ciudad = Ciudad::find($cliente->cityId);
        //Log::info('------- Falla ----', $cliente->toArray());

        DB::commit();
        return [
            'message' => 'Creacion Cliente exitoso!',
            'client' => $cliente,
            'user' => $user,
            'status' => 1,
            'city' =>[
                    'id_city' => $ciudad->id,
                    'name' => $ciudad->name
                ]
        ];

        }catch (\Exception $e){
            DB::rollBack();
            return [
                'message' => 'El error es.'. $e->getMessage(),
                'status' => 3
            ];
        }
    }
    public function update($root ,array $args){
        $clientData = $args['clientRequest'];
        $client = ValidationModels::validationclientInternal($args['id']);

        $clientId = $client->id;
        $user = User::find($client->userId);

        DB::beginTransaction();
        try {
            // Asignar los datos del cliente
            $client->update([
                'firstName'   => $clientData['firstName'] ?? $client->firstName,
                'lastName'    => $clientData['lastName'] ?? $client->lastName,
                'email'       => $clientData['email'] ?? $client->email,
                'phoneNumber' => $clientData['phoneNumber'] ?? $client->phoneNumber,
                'cityId'      => $clientData['cityId'] ?? $client->cityId,
            ]);

            // Actualizar el usuario
            $user->update([
                'email'    => $clientData['email'] ?? $user->email,
                'password' => !empty($clientData['password'])
                    ? Hash::make($clientData['password'])
                    : $user->password,
            ]);

            $ciudad = Ciudad::find($client->cityId);

            DB::commit();

            return [
                'message' => 'Cliente actualizado exitosamente!',
                'client'  => $client,
                'user'    => $user,
                'status'  => 1,
                'city'    => [
                    'id_city' => $ciudad->id,
                    'name'    => $ciudad->name
                ]
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'message' => 'El error es: ' . $e->getMessage(),
                'status' => 3
            ];
        }
    }
    public function delete($root ,array $args){
        $id=Cliente_Interno::find($args['id']);
        if(!$id){
            return ['message'=> 'Borrado no existoso'];
        }else{
            $id->status=0;
            $id->save();
            return ['message'=> 'Borrado existoso'];
        }
    }

    public function updatePhoto($root , array $args){
    try{
        $clientId = $args['id_client'];
        $validators = $this->validateImage($args);

        if ($validators->fails()) {
            return [
                'message' => 'Archivo de imagen inválido.',
                'upcomingmessage' => 'Registre su usuario',
                'status' => 2
            ];
        }
        $client = Cliente_Interno::find($clientId);
        $user = User::find($client->userId);
        $ciudad = Ciudad::find($client->cityId);

            $manager = new ImageManager(new Driver());
            // Manejo de la imagen
            ImageHelper::existDirectorieClient($client->id);
            DB::beginTransaction();
            if (isset($args['photo'])) {
                if ($args['photo'] instanceof UploadedFile) {
                    // Eliminar la foto anterior si existe
                    if ($client->photo) {
                        $path = str_replace(env('APP_URL') . '/storage/', '', $client->photo);
                        Storage::delete('public/' . $path);
                    }

                    $photoPath = $this->processImage($args['photo'], "/client_{$client->id}/photo/{$this->now}.png", $manager);
                    $client->photo = env('APP_URL') . '/storage' . str_replace('public/', '', $photoPath);
                } elseif (is_null($args['photo'])) {
                    if ($client->photo) {
                        $path = str_replace(env('APP_URL'). '/storage/', '', $client->photo);
                        Storage::delete('public/' . $path);
                        $client->photo = null;
                    }
                }
            }
            $client->save();
            DB::commit();
            return[
                'message' => 'Foto de cliente actualizado exitoso!!' ,
                'client' => $client,
                'user'=>$user,
                'status' => 1,
                'city' =>[
                    'id_city' => $ciudad->id,
                    'name' => $ciudad->name
                ]
            ];
        } catch (\Exception $e) {
            DB::rollback();
            return [
                'message'=>'El error es el siguiente. '. $e->getMessage(),
                'status' => 3
            ];
        }
    }

    public function resetPasswordclientAtSupport($root,array $args){
        $id_technician=$args['id_client'];
        $new_password = $args['new_password'];
        try{
            DB::beginTransaction();
            $client=Cliente_Interno::where('id',$id_technician)->first();
            $user=User::where('id',$client->userId)->first();
            $user->password = Hash::make($new_password);
            $user->save();

            event(new PasswordChanged($user));

            DB::commit();
            return [
                'message' => 'Contraseña restablecida para el cliente interno.',
                'result' => true
            ];

        } catch(\Exception $e){
            DB::rollback();
            return [
                'message' => 'Errores en el proceso. ' . $e->getMessage(),
                'result' => false
            ];
        }
    }

    public function updatePhoneClient($root,array $args){
        DB::beginTransaction();
        try{
            $client_id = $args['id'];
            $client = Cliente_Interno::find($client_id);
            $client->phoneNumber = $args['phoneNumber'];
            $client->save();
            DB::commit();
            return [
                'message' => 'Actualzacion correcta del cliente.!',
                'result' => 2
            ];
        } catch(\Exception $e) {
            DB::rollBack();
            return [
                'message' => 'Surgio un problema en la consulta : ' . $e->getMessage(),
                'result' => 3
            ];
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
    private function methodLogin($args):string{
        switch($args){
            case '1':
                return 'formulario';
            case '2':
                return 'google';
            case '3':
                return 'facebook';
            default:
                return 'Método desconocido';
        }
    }

}
