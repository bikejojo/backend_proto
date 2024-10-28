<?php

namespace App\GraphQL\Mutations;

use App\Models\Agenda_Tecnico;
use App\Models\Tecnico;
use App\Models\Tecnico_Habilidad;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use Intervention\Image\Drivers\Gd\Driver;

use function PHPUnit\Framework\isEmpty;

class TecnicoMutations {
    public function create($root, array $args)
{
    $technicianData = $args['technicianRequest'];
    $skill = null;

    // Verificar si el CI ya existe
    if (User::where('ci', $technicianData['ci'])->exists()) {
        return [
            'message' => 'Esta cédula de identidad ya está en uso, por favor intenta con otro.'
        ];
    }

    // Validar longitud del CI
    if (strlen($technicianData['ci']) != 7) {
        return [
            'message' => 'El CI debe tener exactamente 7 dígitos.'
        ];
    }

    // Validar imágenes
    $validators = $this->validateImage($args);
    if ($validators->fails()) {
        return [
            'message' => 'Archivo de imagen inválido.',
            'upcomingmessage' => 'Registre su usuario'
        ];
    }

    // Crear usuario
    $email = strtolower(trim($technicianData['email']));
    $user = User::create([
        'email' => $email,
        'password' => Hash::make($technicianData['password']),
        'ci' => $technicianData['ci'],
        'type_user' => $technicianData['type_user'],
    ]);

    // Crear token de acceso y guardar el usuario
    $tokens = $user->createToken('authToken')->plainTextToken;
    $user->token = $tokens;
    $user->save();

    // Asociar el usuario creado al técnico
    $technicianData['userId'] = $user->id;

    // Crear técnico
    $technician = Tecnico::create($technicianData);

    // Verificar si el técnico se ha creado correctamente
    if (!$technician) {
        return [
            'message' => 'Error al registrar el técnico.'
        ];
    }

    $technicianId = $technician->id;

    // Crear directorios utilizando el ID del técnico
    $this->createTechnicianDirectories($technicianId);

    // Manejo de imágenes utilizando Intervention Image
    $manager = new ImageManager(new Driver());

    // Procesar imagen delantera del carnet
    if (isset($args['frontIdCard']) && $args['frontIdCard'] instanceof UploadedFile) {
        $frontIdCardPath = $this->processImage($args['frontIdCard'], "/{$technicianId}/id_card/front.png", $manager);
        $technician->frontIdCard = str_replace('public/', '', $frontIdCardPath);
    }

    // Procesar imagen trasera del carnet
    if (isset($args['backIdCard']) && $args['backIdCard'] instanceof UploadedFile) {
        $backIdCardPath = $this->processImage($args['backIdCard'], "/{$technicianId}/id_card/back.png", $manager);
        $technician->backIdCard = str_replace('public/', '', $backIdCardPath);
    }

    // Guardar las rutas de las imágenes en el registro del técnico
    $technician->save();
    $id=$technician->id;
    // Crear la agenda para el técnico
    $agenda = Agenda_Tecnico::create([
        'technicianId' => $id,
        'createDate' => Carbon::now()
    ]);

    //Verificar si la agenda se ha creado correctamente
    if (!isset($agenda)) {
        return [
            'message' => 'Error al crear la agenda del técnico.'
        ];
    }
    // Retornar los datos de éxito
    return [
        'message' => 'Registro técnico exitoso',
        'upcomingmessage' => 'Registre sus habilidades',
        'technician' => $technician,
        'user' => $user,
        'skill' => $skill
    ];
}

    public function update($root , array $args){
        $technicianData = $args['technicianRequest'];
        $skillsData = $args['skills'];
        //dd($technicianData);
        if(!isset($technicianData)|| !isEmpty($skillsData)){
            return[
                'message' => 'No existe datos de tecnico'];
        }
        if(!isset($skillsData)|| !isEmpty($skillsData)){
            return [
                'message' => 'no contienen habilidades'];
        }
         // Validar imágenes
        $validators = $this->validateImage($args);
        if ($validators->fails()) {
            return [
                'message' => 'Archivo de imagen inválido.'
            ];
        }
        $technicianId = $args['id'];
        $technician = Tecnico::find($technicianId);
        $userId = $technician->userId;
        $user = User::find($userId);
        //actualizar user para todo
        $email = strtolower(trim($technicianData['email']));
        $user->email = $email;
        $user->password = Hash::make($technicianData['password']);
        $user->type_user = $technicianData['type_user'];
        $user->save();
        //
        $technician->firstName = $technicianData['firstName'];
        $technician->lastName = $technicianData['lastName'];
        $technician->email = $email;
        $technician->phoneNumber = $technicianData['phoneNumber'];
        $technician->password = Hash::make($technicianData['password']);
        $technician->userId = $user->id;
        $technician->cityId = $technicianData['cityId'];
        $technician->save();

        $technicianId = $technician->id;
        // Crear directorios utilizando el ID del técnico
        $this->createTechnicianDirectories($technicianId);
        $isPhotoUploaded = isset($args['photo']) && $args['photo'] instanceof UploadedFile;
        $isFrontIdCardUploaded = isset($args['frontIdCard']) && $args['frontIdCard'] instanceof UploadedFile;
        $isBackIdCardUploaded = isset($args['backIdCard']) && $args['backIdCard'] instanceof UploadedFile;
        $manager = new ImageManager(new Driver());
        if ($isPhotoUploaded || $isFrontIdCardUploaded || $isBackIdCardUploaded) {
            // Procesar cada archivo solo si fue enviado en la solicitud

            if ($isPhotoUploaded) {
                // Eliminar foto anterior
                if ($technician->photo) {
                    Storage::disk('public')->delete($technician->photo);
                }
                $photoCardPath = $this->processImage($args['photo'], "/{$technicianId}/profile/photo.png", $manager);
                $technician->photo = str_replace('public/', '', $photoCardPath);
            }

            if ($isFrontIdCardUploaded) {
                // Eliminar frente del carnet anterior
                if ($technician->frontIdCard) {
                    Storage::disk('public')->delete($technician->frontIdCard);
                }
                $frontIdCardPath = $this->processImage($args['frontIdCard'], "/{$technicianId}/id_card/front.png", $manager);
                $technician->frontIdCard = str_replace('public/', '', $frontIdCardPath);
            }

            if ($isBackIdCardUploaded) {
                // Eliminar reverso del carnet anterior
                if ($technician->backIdCard) {
                    Storage::disk('public')->delete($technician->backIdCard);
                }
                $backIdCardPath = $this->processImage($args['backIdCard'], "/{$technicianId}/id_card/back.png", $manager);
                $technician->backIdCard = str_replace('public/', '', $backIdCardPath);
            }

            // Guardar las rutas de las imágenes en el técnico solo si se han actualizado
            $technician->save();
        }
        // HABILIDADES DEL TECNICO
        $habilidades=[];
        foreach($skillsData as $skill){
                $existSkill = Tecnico_Habilidad::where('technicianId',$technicianId)
                ->where('skillId',$skill['skillId'])
                ->first();
                //dd($existSkill);
                if($existSkill){
                    $existSkill->experience = $skill['experience'];
                    $existSkill->save();
                    $habilidades[] = $existSkill;
                }else{
                    $newSkill = Tecnico_Habilidad::create([
                        'technicianId' => $technicianId,
                        'skillId' => $skill['skillId'],
                        'experiencie' => $skill['experience']
                    ]);
                    $habilidades[] = $newSkill;
                }
        }

        return[
            'message' => 'Tecnico actualizado exitoso',
            'upcomingmessage' => 'Actualizacion de sus habilidades',
            'technician' => $technician,
            'user' => $user,
            'skill' => $habilidades
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
