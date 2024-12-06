<?php

namespace App\GraphQL\Mutations;

use Illuminate\Support\Facades\DB;
use App\Models\Agenda_Tecnico;
use App\Models\Tecnico;
use App\Models\Tecnico_Habilidad;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use App\Helpers\ImageHelper;

use function PHPUnit\Framework\isEmpty;

class TecnicoMutations {
    protected $app;
    protected $now;

    public function __construct() {
        $this->app= env('FULL_URL');
        $this->nowFront= Carbon::now()->format('Ymd_His');
        $this->nowBack=Carbon::now()->addMinute(1);
    }

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
        $validators = ImageHelper::validateImage($args);
        if ($validators->fails()) {
            return [
                'message' => 'Archivo de imagen inválido.',
                'upcomingmessage' => 'Registre su usuario'
            ];
        }
        DB::beginTransaction();
        // Si pasa todas las validaciones, crear usuario y técnico
        try {
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
            $technician = Tecnico::create([
                'firstName' => $technicianData['firstName'],
                'lastName' => $technicianData['lastName'],
                'email' => $technicianData['email'],
                'phoneNumber' => $technicianData['phoneNumber'],
                'password' => Hash::make($technicianData['password']),
                'userId' => $technicianData['userId'],
                'cityId' => $technicianData['cityId'],
            ]);

            if (!$technician) {
                return [
                    'message' => 'Error al registrar el técnico.'
                ];
            }

            $technicianId = $technician->id;
            $value = $user->type_user;
            ImageHelper::createDirectorie($technicianId,$value);
            $manager = new ImageManager(new Driver());
            // Procesar imagen delantera del carnet
            $this->nowBack=$this->nowBack->format('Ymd_His');
            
            if (isset($args['frontIdCard']) && $args['frontIdCard'] instanceof UploadedFile) {
                $frontIdCardPath = ImageHelper::processImage($args['frontIdCard'], "/{$technicianId}/id_card/"."{$this->nowFront}.png", $manager);
                $technician->frontIdCard = $this->app . '/storage' . str_replace('public/', '', $frontIdCardPath);
            }

            // Procesar imagen trasera del carnet
            if (isset($args['backIdCard']) && $args['backIdCard'] instanceof UploadedFile) {
                $backIdCardPath = ImageHelper::processImage($args['backIdCard'], "/{$technicianId}/id_card/"."{$this->nowBack}.png", $manager);
                $technician->backIdCard = $this->app . '/storage' . str_replace('public/', '', $backIdCardPath);
            }

            // Guardar las rutas de las imágenes en el registro del técnico
            $technician->save();
            // Crear la agenda para el técnico
            $agenda = Agenda_Tecnico::create([
                'technicianId' => $technicianId,
                'createDate' => Carbon::now()
            ]);

            if (!isset($agenda)) {
                return [
                    'message' => 'Error al crear la agenda del técnico.'
                ];
            }
            // Retornar los datos de éxito
            DB::commit();
            return [
                'message' => 'Registro técnico exitoso',
                'upcomingmessage' => 'Registre sus habilidades',
                'technician' => $technician,
                'user' => $user,
                'skills' => $skill
            ];
            
        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'message' => 'Ocurrió un error al crear el técnico: ' . $e->getMessage()
            ];
        }
    }

    public function update($root , array $args){
        $technicianData = $args['technicianRequest'];
        if(!isset($technicianData)){
            return[
                'message' => 'No existe datos de tecnico'];
        }
        
        $validators = ImageHelper::validateImage($args);
        if ($validators->fails()) {
            return [
                'message' => 'Archivo de imagen inválido.'
            ];
        }
        $technicianId = $args['id'];
        $technician = Tecnico::find($technicianId);
        $userId = $technician->userId;
        $user = User::find($userId);

        if(!isset($technicianData['password'])){
            $technicianData['password'] = $user->password;
        }
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
        $value = $user->type_user;
        // Crear directorios utilizando el ID del técnico
        ImageHelper::deleteDirectoryIdCard($technicianId);
        ImageHelper::createDirectorie($technicianId,$value);
       
        $this->nowBack=$this->nowBack->format('Ymd_His');
        $isFrontIdCardUploaded = isset($args['frontIdCard']) && $args['frontIdCard'] instanceof UploadedFile;
        $isBackIdCardUploaded = isset($args['backIdCard']) && $args['backIdCard'] instanceof UploadedFile;
        $manager = new ImageManager(new Driver());
        if ($isFrontIdCardUploaded || $isBackIdCardUploaded) {
            // Procesar cada archivo solo si fue enviado en la solicitud
            if ($isFrontIdCardUploaded) {

                $frontIdCardPath = ImageHelper::processImage($args['frontIdCard'], "/{$technicianId}/id_card/"."{$this->nowFront}.png", $manager);
                $technician->frontIdCard =$this->app.'/storage' . str_replace('public/', '', $frontIdCardPath);
            }

            if ($isBackIdCardUploaded) {

                $backIdCardPath = ImageHelper::processImage($args['backIdCard'], "/{$technicianId}/id_card/"."{$this->nowBack}.png", $manager);
                $technician->backIdCard =$this->app.'/storage' . str_replace('public/', '', $backIdCardPath);
            }

            // Guardar las rutas de las imágenes en el técnico solo si se han actualizado
            $technician->save();
        }
        $skillsData = Tecnico_Habilidad::where('technicianId',$technicianId)
        ->get();
        $ha=[];
            foreach($skillsData as $habilidad_tec) {
                if ($habilidad_tec->skill) {
                    // Añadimos los detalles de la habilidad al array
                    $ha[] = [
                        'id_skill' => $habilidad_tec->skill->id,
                        'name' => $habilidad_tec->skill->name,
                        'experience' => $habilidad_tec->experience,
                    ];
                }
            }

        return[
            'message' => 'Tecnico actualizado exitoso',
            'upcomingmessage' => 'Actualizacion de sus habilidades',
            'technician' => $technician,
            'user' => $user,
            'skills' => $ha
        ];
    }

    public function photoUpdate($root ,array $args){
        $photoTechnicialId = $args['id'];
        $technicial = Tecnico::find($photoTechnicialId);
        $validators = ImageHelper::validateImagePhoto($args);
        if ($validators->fails()) {
            return [
                'message' => 'Archivo de imagen inválido.'
            ];
        }
        $technicialId = $technicial->id;
        $userId = $technicial->userId;
        $user = User::find($userId);
        $manager = new ImageManager(new Driver());
        $isPhotoUploaded = isset($args['photo']) && $args['photo'] instanceof UploadedFile;
        if ($isPhotoUploaded) {
            // Eliminar foto anterior
            ImageHelper::deleteDirectoryProfile($technicialId);

            $photoCardPath = ImageHelper::processImage($args['photo'], "/{$technicialId}/photo/"."{$this->nowFront}.png", $manager);
            $technicial->photo =$this->app.'/storage' . str_replace('public/', '', $photoCardPath);
        }
        $technicial->save();
        $skillsData = Tecnico_Habilidad::where('technicianId',$technicialId)
        ->get();
        $ha=[];
        foreach($skillsData as $habilidad_tec) {
            if ($habilidad_tec->skill) {
                // Añadimos los detalles de la habilidad al array
                $ha[] = [
                    'id_skill' => $habilidad_tec->skill->id,
                    'name' => $habilidad_tec->skill->name,
                    'experience' => $habilidad_tec->experience,
                ];
            }
        }
        return[
            'message' => 'Foto de tecnico actualizado exitoso',
            'technician' => $technicial,
            'user' => $user,
            'skills' => $ha
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


}
