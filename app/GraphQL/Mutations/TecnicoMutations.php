<?php

namespace App\GraphQL\Mutations;

use App\Models\Tecnico;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\Drivers\Gd\Encoders\WebpEncoder;

use Intervention\Image\Imagick\Driver as ImagickDriver;
use Nuwave\Lighthouse\Schema\Types\Scalars\Upload;

class TecnicoMutations {
    public function create($root,array $args){
        $tecnicoData = $args['tecnicoRequest'];
        // Handle files
        // Manejar los archivos
        $manager = new ImageManager(new Driver());
        //$manager->configure(['driver' => 'gd']);

        $validator = validator::make($args ,[
            'carnet_anverso' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'carnet_reverso' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
            'foto' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
        ]);

        if($validator->fails()){
            throw new \Exception('archivo de imagen invalido');
        }

        if (isset($args['carnet_anverso']) && $args['carnet_anverso'] instanceof UploadedFile) {
            /*$carnetAnversoPath = $args['carnet_anverso']->store('carnets', 'carnets');
            $tecnicoData['carnet_anverso'] = $carnetAnversoPath;*/
            $image = $manager->read($args['carnet_anverso']->getRealPath());
            //redimensionar
            $image->resize(800,nulL, function($constraint){
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $carnetAnversoPath = 'carnets/' . uniqid() . '.png';
            $fullPath = storage_path('app/public/' . $carnetAnversoPath);
            $image->save($fullPath, 75, 'png');
        }

        /*if (isset($args['carnet_reverso']) && $args['carnet_reverso'] instanceof UploadedFile) {
            $carnetReversoPath2 = $args['carnet_reverso']->store('carnets', 'carnets');
            $tecnicoData['carnet_reverso'] = $carnetReversoPath2;
        }*/
        if(isset($args['carnet_reverso']) && $args['carnet_reverso'] instanceof UploadedFile){
            $image = $manager->read($args['carnet_reverso']->getRealPath());

            $image->resize(800,null,function($constraint){
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $carnetReversoPath2 = 'carnets/'.uniqid(). '.png';
        $fullPath = storage_path('app/public/'.$carnetReversoPath2);
        $image->save($fullPath,7,'png');
        }
        /*if (isset($args['foto']) && $args['foto'] instanceof UploadedFile) {
            $fotoPath = $args['foto']->store('fotos', 'fotos');
            $tecnicoData['foto'] = $fotoPath;
        }*/
        if(isset($args['foto'])&&$args['foto'] instanceof UploadedFile){
            $image = $manager->read($args['foto']->getRealPath());
            $image->resize(800,null,function($constraint){
                $constraint->aspectRatio();
                $constraint->upsize();
            });
            $foto = 'fotos/' . uniqid() . '.png';
            $fullPath = storage_path('app/public/'. $foto);
            $image->save($fullPath,75,'png');
        }
        //$urlCarnetAnverso = Storage::disk('carnet')->url($carnetAnversoPath1);
        //$urlFoto = Storage::disk('fotos')->url($fotoPath);
        $tecnico = Tecnico::create($tecnicoData);

        return $tecnico;
    }

    public function update($root,array $args){
        $tecnicoData = $args['tecnicoRequest'];
        $tecnico = Tecnico::find($args['id']);

        if(!$tecnico){
            throw new \Exception('Tecnico no encontrado');
        }

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
