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

class TecnicoMutations {
    protected $app;
    protected $nowFront;
    protected $nowBack;
    protected $nowProfile;

    public function __construct() {
        $this->app= env('FULL_URL');
        $this->nowFront= Carbon::now()->format('Ymd_His');
        $this->nowBack=Carbon::now()->addMinute(1);
        $this->nowProfile=Carbon::now()->addMinutes(2);
    }

    public function create($root, array $args)
    {
        $technicianData = $args['technicianRequest'];
        $skill = null;
        // Verificar si el CI ya existe
        if (strlen($technicianData['ci']) != 7 || User::where('ci', $technicianData['ci'])->exists()) {
            return [
                'message' => 'El CI debe tener 7 digitos y no estar en uso.'
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
                'type_user' => 1,
            ]);

            // Crear token de acceso y guardar el usuario
            $tokens = $user->createToken('authToken')->plainTextToken;
            $user->token = $tokens;
            $user->save();

            // Crear técnico
            $technician = Tecnico::create([
                'firstName' => $technicianData['firstName'],
                'lastName' => $technicianData['lastName'],
                'email' => $technicianData['email'],
                'phoneNumber' => $technicianData['phoneNumber'],
                'password' => Hash::make($technicianData['password']),
                'userId' => $user->id,
                'cityId' => $technicianData['cityId'],
                'status' => 1,
                'average_rating'=>5.00
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
            $this->nowProfile=$this->nowProfile->format('Ymd_His');
            ImageHelper::existDirectorieCard($technicianId);
            if (isset($args['frontIdCard']) && $args['frontIdCard'] instanceof UploadedFile) {
                $frontIdCardPath = ImageHelper::processImage($args['frontIdCard'], "/{$technicianId}/id_card/"."{$this->nowFront}.png", $manager);
                $technician->frontIdCard = $this->app . '/storage' . str_replace('public/', '', $frontIdCardPath);
            }

            // Procesar imagen trasera del carnet
            if (isset($args['backIdCard']) && $args['backIdCard'] instanceof UploadedFile) {
                $backIdCardPath = ImageHelper::processImage($args['backIdCard'], "/{$technicianId}/id_card/"."{$this->nowBack}.png", $manager);
                $technician->backIdCard = $this->app . '/storage' . str_replace('public/', '', $backIdCardPath);
            }

            ImageHelper::existDirectorie($technicianId);
            //dd($args['photo']);
            if (isset($args['photo']) && $args['photo'] instanceof UploadedFile){
                $profilePath = ImageHelper::processImage($args['photo'],"/{$technicianId}/profile/"."{$this->nowProfile}.png",$manager);
                $technician->photo = $this->app . '/storage' . str_replace('public/','',$profilePath);
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

    public function update($root, array $args)
    {
        $technicianData = $args['technicianRequest'] ?? null;

        if (!$technicianData) {
            return ['message' => 'No existe datos de técnico'];
        }

        $validators = ImageHelper::validateImage($args);
        if ($validators->fails()) {
            return ['message' => 'Archivo de imagen inválido.'];
        }

        $technicianIds = $args['id'];
        $technician = Tecnico::find($technicianIds);

        if (!$technician) {
            return ['message' => 'El técnico no fue encontrado.'];
        }

        $user = User::find($technician->userId);

        DB::beginTransaction();
        try {
            // Actualizar datos del usuario
            $user->update([
                'email' => strtolower(trim($technicianData['email'] ?? $user->email)),
                'password' => !empty($technicianData['password'])
                    ? Hash::make($technicianData['password'])
                    : $user->password,
                'type_user' => $technicianData['type_user'] ?? $user->type_user,
            ]);

            // Actualizar datos del técnico
            $technician->update([
                'firstName' => $technicianData['firstName'] ?? $technician->firstName,
                'lastName' => $technicianData['lastName'] ?? $technician->lastName,
                'email' => $user->email,
                'phoneNumber' => $technicianData['phoneNumber'] ?? $technician->phoneNumber,
                'cityId' => $technicianData['cityId'] ?? $technician->cityId,
            ]);

            // Crear/Eliminar directorios
            ImageHelper::deleteDirectoryIdCard($technician->id);
            ImageHelper::createDirectorie($technician->id, $user->type_user);

            // Procesar imágenes de tarjetas de identificación
            $manager = new ImageManager(new Driver());
            if (!empty($args['frontIdCard']) || !empty($args['backIdCard'])) {
                ImageHelper::existDirectorieCard($technician->id);

                if (!empty($args['frontIdCard'])) {
                    $frontIdCardPath = ImageHelper::processImage($args['frontIdCard'], "/{$technician->id}/id_card/{$this->nowFront}.png", $manager);
                    $technician->frontIdCard = $this->app . '/storage' . str_replace('public/', '', $frontIdCardPath);
                }

                if (!empty($args['backIdCard'])) {
                    $backIdCardPath = ImageHelper::processImage($args['backIdCard'], "/{$technician->id}/id_card/{$this->nowBack}.png", $manager);
                    $technician->backIdCard = $this->app . '/storage' . str_replace('public/', '', $backIdCardPath);
                }

                $technician->save();
            }

            // Obtener habilidades del técnico
            $skillsData = Tecnico_Habilidad::where('technicianId', $technician->id)->get();
            $skills = $skillsData->map(function ($skill) {
                return [
                    'id_skill' => $skill->skill->id,
                    'name' => $skill->skill->name,
                    'experience' => $skill->experience,
                ];
            });

            DB::commit();

            return [
                'message' => 'Técnico actualizado exitosamente.',
                'upcomingmessage' => 'Actualización de sus habilidades',
                'technician' => $technician,
                'user' => $user,
                'skills' => $skills
            ];

        } catch (\Exception $e) {
            DB::rollBack();
            return [
                'message' => 'El error es: ' . $e->getMessage(),
                'status' => 3
            ];
        }
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
        ImageHelper::existDirectorie($technicialId);
        $userId = $technicial->userId;
        $user = User::find($userId);
        $manager = new ImageManager(new Driver());
        $isPhotoUploaded = isset($args['photo']) && $args['photo'] instanceof UploadedFile;
        if ($isPhotoUploaded) {
            // Eliminar foto anterior
            ImageHelper::deleteDirectoryProfile($technicialId);
            $this->nowProfile=$this->nowProfile->format('Ymd_His');
            $photoCardPath = ImageHelper::processImage($args['photo'], "/{$technicialId}/profile/"."{$this->nowProfile}.png", $manager);
            $technicial->photo =$this->app.'/storage' . str_replace('public/', '', $photoCardPath);
        }
        $technicial->save();
        $skillsData = Tecnico_Habilidad::where('technicianId',$technicialId)
        ->get();
        $ha=[];
        foreach($skillsData as $habilidad_tec) {
            if ($habilidad_tec->skill) {
                // Añadimos los detalles de la habilidad al arr
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

    public function resetPasswordTechnicianAtSupport($root,array $args){
        DB::beginTransaction();
        try{
            $id_technician=$args['id_technician'];
            $new_password = $args['new_password'];
            $technician = Tecnico::where('id',$id_technician)->first();
            //dd($technician);
            $user=User::where('id',$technician->userId)->first();
            $user->password=Hash::make($new_password);
            $user->save();
            DB::commit();
            return [
                'message' => 'Contraseña restablecida para el tecnico.',
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

    public function delete($root, array $args){
        $technician = Tecnico::find($args['id']);
        if (!$technician) {
            return ['message' => 'Eliminacion no exitosa del tecnico'];
        }
        // Borrar técnico
        $technician->status = 0;
        $technician->save();
        $user=User::where('id',$technician->userId)->update(['status', 0 ]);
            return ['message' => 'Eliminacion exitosa del tecnico'];
    }


}
