<?php

namespace App\GraphQL\Mutations;

use App\Models\Tecnico;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Hash;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;

class TecnicoMutations {
public function create($root,array $args){

    $tecnicoData = $args['tecnicoRequest'];
     // se crea instancia + el driver de gd
    // Validación de formato de imágenes
    $validatos = $this->validaImage($args);
    if ($validatos->fails())
        throw new \Exception('Archivo de imagen inválido');

    // Crear técnico con datos iniciales
    $tecnico = Tecnico::create($tecnicoData);
    $tecnicoId = $tecnico->id;

    // Crear directorios utilizando el tecnico_id para la ruta
    $this->directoriesTecnico($tecnicoId);
    $manager = new ImageManager(new Driver());
    // Manejo de las imágenes
    if (isset($args['carnet_anverso']) && $args['carnet_anverso'] instanceof UploadedFile) {
        $carnetAnversoPath = $this->processImage($args['carnet_anverso'], "$tecnicoId/carnet/Anverso_" . uniqid() . ".png", $manager);
        $tecnico->carnet_anverso = str_replace('public/', '', $carnetAnversoPath);
    }

    if (isset($args['carnet_reverso']) && $args['carnet_reverso'] instanceof UploadedFile) {
        $carnetReversoPath = $this->processImage($args['carnet_reverso'], "$tecnicoId'/carnet/Reverso_" . uniqid() . '.png',$manager);
        $tecnico->carnet_reverso = str_replace('public/','',$carnetReversoPath);
    }
    //no se guarda foto de perfil a crearlo
    // Guardar las rutas de las imágenes en el técnico
    $tecnico->save();
    return $tecnico;
}

    public function update($root,array $args){
        $tecnicoData = $args['tecnicoRequest'];
        $tecnico = Tecnico::find($args['id']);

        if(!$tecnico)
            throw new \Exception('Tecnico no encontrado');

        $tecnico->nombre = $tecnicoData['nombre']??$tecnico->nombre;
        $tecnico->apellido = $tecnicoData['apellido']??$tecnico->apellido;
        $tecnico->email = $tecnicoData['email']??$tecnico->email;
        $tecnico->telefono = $tecnicoData['telefono'] ?? $tecnico->telefono;
        $tecnico->contrasenia = isset($tecnicoData['contrasenia']) ? Hash::make($tecnicoData['contrasenia']): $tecnico->contrasenia;
        $tecnico->foto = $tecnicoData['foto'] ?? $tecnico->foto;
        $tecnico->users_id = $tecnicoData['users_id'] ?? $tecnico->users_id;
        $tecnico->ciudades_id = $tecnicoData['ciudades_id'] ?? $tecnico->ciudades_id;

        $manager = new ImageManager(new Driver());
        /*if(isset($args['carnet_anverso']) && $args['carnet_anverso'] instanceof UploadedFile){
            if ($tecnico->carnet_anverso) {
                Storage::delete('public/' . $tecnico->carnet_anverso);
            }
            $carnetAnversoPath = $this->processImage($args['carnet_anverso'],"$tecnico/carnet/Anverso_".uniqid().".png" , $manager );
            $tecnico->carnet_anverso = str_replace('public/', '', $carnetAnversoPath);
        }

        if(isset($args['carnet_reverso']) && $args['carnet_reverso'] instanceof UploadedFile){
            if ($tecnico->carnet_reverso) {
                Storage::delete('public/' . $tecnico->carnet_reverso);
            }
            $carnetReversoPath = $this->processImage($args['carnet_reverso'],"$tecnico/carnet/Reverso_".uniqid().".png" , $manager );
            $tecnico->carnet_anverso = str_replace('public/', '', $carnetReversoPath);
        }*/
        if(isset($args['foto']) && $args['foto'] instanceof UploadedFile){
            if ($tecnico->carnet_reverso) {
                Storage::delete('public/' . $tecnico->carnet_reverso);
            }
            $fotoPath = $this->processImage($args['foto'],"$tecnico/perfil/foto_".uniqid().".png" , $manager );
            $tecnico->foto = str_replace('public/', '', $fotoPath);
        }
        $tecnico->save();
        return $tecnico;
    }

    public function delete($root,array $args){
        $id=Tecnico::find($args['id']);
        if($id){
            return ['message' => 'Borrado No exitoso'];
        }else{
            $tecnico = Tecnico::where('id',$id)->delete();
            return ['message' => 'Borrado exitoso'];
        }
    }

    private function validaImage($args){
        return validator::make($args, [
            'carnet_anverso' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'carnet_reverso' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'foto' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
        ]);
    }

    private function directoriesTecnico($tecnicoId){
        Storage::makeDirectory('public/'.$tecnicoId . '/carnet');
        Storage::makeDirectory('public/'.$tecnicoId . '/perfil');
    }
    private function processImage(UploadedFile $file,$path,$manager){
        $image = $manager->read($file->getRealPath());
        $image->resize(700, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $fullPath = storage_path("app/public/$path");
        $image->save($fullPath, 75, 'png');

        return $path;
    }
}
