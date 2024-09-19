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
    $manager = new ImageManager(new Driver()); // se crea instancia + el driver de gd
          // Validación de formato de imágenes
   /* $validator = validator::make($args, [
        'carnet_anverso' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
        'carnet_reverso' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
        'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
    ]);

    if ($validator->fails())
        throw new \Exception('Archivo de imagen inválido');*/

    // Crear técnico con datos iniciales
    $tecnico = Tecnico::create($tecnicoData);
    $tecnicoId = $tecnico->id;

    // Crear directorios utilizando el tecnico_id para la ruta
    $tecnicoDir = 'public/' . $tecnicoId;
    Storage::makeDirectory($tecnicoDir . '/carnet');
    Storage::makeDirectory($tecnicoDir . '/perfil');

    // Manejo de las imágenes
    if (isset($args['carnet_anverso']) && $args['carnet_anverso'] instanceof UploadedFile) {
        $image = $manager->read($args['carnet_anverso']->getRealPath());
        $image->resize(700, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $carnetAnversoPath = $tecnicoId . '/carnet/Anverso_' . uniqid() . '.png';
        $fullPath = storage_path('app/public/' . $carnetAnversoPath);

        $image->save($fullPath, 75, 'png');
        $tecnico->carnet_anverso =str_replace('public/', '', $carnetAnversoPath);
        $compressedUrl1 = url('storage/'  . $tecnico->carnet_anverso);
    }

    if (isset($args['carnet_reverso']) && $args['carnet_reverso'] instanceof UploadedFile) {
        $image = $manager->read($args['carnet_reverso']->getRealPath());
        $image->resize(700, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $carnetReversoPath2 = $tecnicoId . '/carnet/Reverso_' . uniqid() . '.png';
        $fullPath = storage_path('app/public/' . $carnetReversoPath2);
        $image->save($fullPath, 75, 'png');
        $tecnico->carnet_reverso = str_replace('public/','',$carnetReversoPath2);
        $compressedUrl2 = url('storage/' . $tecnico->carnet_reverso);
    }

    /*if (isset($args['foto']) && $args['foto'] instanceof UploadedFile) {
        $image = $manager->read($args['foto']->getRealPath());
        $image->resize(700, null, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });

        $fotoPath = $tecnicoId . '/perfil/foto_' . uniqid() . '.png';
        $fullPath = storage_path('app/public/' . $fotoPath);

        $image->save($fullPath, 75, 'png');
        $tecnico->foto = str_replace('public/','',$fotoPath);
    }*/

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
        $tecnico->carnet_anverso = $tecnicoData['carnet_anverso'] ?? $tecnico->carnet_anverso;
        $tecnico->carnet_reverso = $tecnicoData['carnet_reverso']?? $tecnico->carnet_reverso;
        $tecnico->email = $tecnicoData['email']??$tecnico->email;
        $tecnico->telefono = $tecnicoData['telefono'] ?? $tecnico->telefono;
        $tecnico->contrasenia = isset($tecnicoData['contrasenia']) ? Hash::make($tecnicoData['contrasenia']): $tecnico->contrasenia;
        $tecnico->foto = $tecnicoData['foto'] ?? $tecnico->foto;
        $tecnico->users_id = $tecnicoData['users_id'] ?? $tecnico->users_id;
        $tecnico->ciudades_id = $tecnicoData['ciudades_id'] ?? $tecnico->ciudades_id;

        $manager = new ImageManager(new Driver());

        if(isset($args['carnet_anverso']) && $args['carnet_anverso'] instanceof UploadedFile){
            if ($tecnico->carnet_anverso) {
                Storage::delete('public/' . $tecnico->carnet_anverso);
            }
            $image = $manager->read($args['carnet_anverso']->getRealPath());
            $image->resize(700,null,function($constraint){
                $constraint->aspectRadio();
                $constraint->upsize();
            });
            $carnetAnversoPath = $tecnico->id. '/carnet/Anverso_' . uniqid() . '.png';
            $fullPath = storage_path('app/public/'.$carnetAnversoPath);
            $image->save($fullPath,75,'png');
            $tecnico->carnet_anverso = str_replace('public/','',$carnetAnversoPath);
        }

        if(isset($args['carnet_reverso']) && $args['carnet_reverso'] instanceof UploadedFile){
            if ($tecnico->carnet_reverso) {
                Storage::delete('public/' . $tecnico->carnet_reverso);
            }
            $image = $manager->read($args['carnet_reverso']->getRealPath());
            $image->resize(700,null,function($constraint){
                $constraint->aspectRadio();
                $constraint->upsize();
            });
            $carnetReversoPath = $tecnico->id. '/carnet/Reverso_' . uniqid() . '.png';
            $fullPath = storage_path('app/public/'.$carnetReversoPath);
            $image->save($fullPath,75,'png');
            $tecnico->carnet_reverso = str_replace('public/','',$carnetReversoPath);
        }

        if(isset($args['foto']) && $args['foto'] instanceof UploadedFile){
            if ($tecnico->carnet_reverso) {
                Storage::delete('public/' . $tecnico->carnet_reverso);
            }
            $image = $manager->read($args['foto']->getRealPath());
            $image->resize(700,null,function($constraint){
                $constraint->aspectRadio();
                $constraint->upsize();
            });
            $fotoPath = $tecnico->id. '/perfil/foto_' . uniqid() . '.png';
            $fullPath = storage_path('app/public/'.$fotoPath);
            $image->save($fullPath,75,'png');
            $tecnico->foto = str_replace('public/','',$fotoPath);
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

}
