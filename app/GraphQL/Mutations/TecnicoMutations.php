<?php

namespace App\GraphQL\Mutations;

use App\Models\Tecnico;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;

class TecnicoMutations {
    public function create($root, array $args)
    {
        $technicianData = $args['technicianRequest'];
        $userId = $technicianData['userId'];
        $user = User::find($userId);
        // Validar formato de imágenes
        $validators = $this->validateImage($args);
        if ($validators->fails()) {
            throw new \Exception('Invalid image file.');
        }
            // Crear técnico con datos iniciales
        $technician = Tecnico::create($technicianData);
        $technicianId = $technician->id;

        // Crear directorios utilizando technicianId para la ruta
        $this->createTechnicianDirectories($technicianId);
        $manager = new ImageManager(new Driver());

        // Manejo de las imágenes
        if (isset($args['frontIdCard']) && $args['frontIdCard'] instanceof UploadedFile) {
            $frontIdCardPath = $this->processImage($args['frontIdCard'], "$technicianId/id_card/front.png", $manager);
            $technician->frontIdCard = str_replace('public/', '', $frontIdCardPath);
        }

        if (isset($args['backIdCard']) && $args['backIdCard'] instanceof UploadedFile) {
            $backIdCardPath = $this->processImage($args['backIdCard'], "$technicianId/id_card/back.png", $manager);
            $technician->backIdCard = str_replace('public/', '', $backIdCardPath);
        }

        if (isset($args['photo']) && $args['photo'] instanceof UploadedFile) {
            $photoPath = $this->processImage($args['photo'], "$technicianId/profile/photo.png", $manager);
            $technician->photo = str_replace('public/', '', $photoPath);
        }

        // Guardar las rutas de las imágenes en el técnico
        $technician->save();
        //return $technician;
        return [
            'message'=> ' Registro tecnico exitoso!' ,
            'upcomingmessage' => 'Registre sus habilidades' ,
            'technician' => $technician,
            'user' => $user
        ];
    }
    public function update($root, array $args){
        $technicianData = $args['technicianRequest'];
        $technician = Tecnico::find($args['id']);
        $technicianId = $technician->id;
        $user = User::find($technicianData['userId']);

        if ($technician==null){
            throw new \Exception('Technician not found.');
        }
        // Actualización de los datos del técnico
        $firstName = trim($technicianData['firstName']);
        $lastName = trim($technicianData['lastName']);
        $email = trim($technicianData['email']);
        $phoneNumber = trim($technicianData['phoneNumber']);
        ####################################
        $technician->firstName = $firstName;
        $technician->lastName = $lastName;
        $technician->email = $email;
        $technician->phoneNumber = $phoneNumber;
        $technician->password = isset($technicianData['password']) ? Hash::make($technicianData['password']) : $technician->password;
        $technician->photo = $technicianData['photo'] ?? $technician->photo;
        $technician->userId = $technicianData['userId'] ?? $technician->userId;
        $technician->cityId = $technicianData['cityId'] ?? $technician->cityId;

        // Manejo de la imagen de perfil
        $manager = new ImageManager(new Driver());
        if (isset($args['photo']) && $args['photo'] instanceof UploadedFile) {
            //dd($technician->photo);
            if ($technician->photo) {
                Storage::delete('public/' . $technician->photo);
            }
            $photoPath = $this->processImage($args['photo'], "$technicianId/profile/photo.png", $manager);
            $technician->photo = str_replace('public/', '', $photoPath);
        }
        // Guardar cambios del técnico
        $technician->save();
        // Actualizar datos del usuario relacionado
        $user->email = $technicianData['email'] ?? $user->email;
        $user->save();

    //return $technician;
        return [
            'technician' => $technician ,
            'message' => 'Tecnico actualizado exitoso!'
        ];
    }

    public function delete($root, array $args){
    $technician = Tecnico::find($args['id']);
    if (!$technician) {
        throw new \Exception('Technician not found.');
    }
    // Borrar técnico
    $technician->delete();

    return ['message' => 'Eliminacion exitosa del tecnico'];
    }
// Validación de imágenes
    private function validateImage($args){
        return Validator::make([
        'frontIdCard' => $args['frontIdCard'] ?? null ,
        'backIdCard'=> $args['backIdCard'] ?? null ,
        'photo' => $args['photo'] ?? null ,
        ], [
            'frontIdCard' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'backIdCard' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
        ]);
    }

    private function createTechnicianDirectories($technicianId){
        Storage::makeDirectory('public/' . $technicianId . '/id_card');
        Storage::makeDirectory('public/' . $technicianId . '/profile');
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
