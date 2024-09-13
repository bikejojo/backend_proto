<?php

namespace App\GraphQL\Mutations;

use App\Models\Tecnico;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;

class TecnicoMutations {
    public function create($root,array $args){
        $tecnicoData = $args['tecnicoRequest'];
        // Handle files
        // Manejar los archivos
        if (isset($args['carnet_anverso']) && $args['carnet_anverso'] instanceof UploadedFile) {
            $carnetAnversoPath1 = $args['carnet_anverso']->store('carnets', 'carnet');
            $tecnicoData['carnet_anverso'] = $carnetAnversoPath1;
        }

        if (isset($args['carnet_reverso']) && $args['carnet_reverso'] instanceof UploadedFile) {
            $carnetReversoPath2 = $args['carnet_reverso']->store('carnets', 'carnet');
            $tecnicoData['carnet_reverso'] = $carnetReversoPath2;
        }
        // Verificar si el archivo fue guardado
        if (Storage::disk('carnet')->exists($carnetAnversoPath1)) {
            logger("El archivo de carnet anverso se guardó correctamente en: " . $carnetAnversoPath1);
        } else {
            logger("Hubo un problema al guardar el archivo de carnet anverso.");
        }

        if (isset($args['foto']) && $args['foto'] instanceof UploadedFile) {
            $fotoPath = $args['foto']->store('fotos', 'foto');
            $tecnicoData['foto'] = $fotoPath;
        }

        //$urlCarnetAnverso = Storage::disk('carnet')->url($carnetAnversoPath1);
        //$urlFoto = Storage::disk('fotos')->url($fotoPath);
        $tecnico = Tecnico::create($tecnicoData);

        return $tecnico;
    }

    public function update($root,array $args){
        /*$id=Tecnico::find($args['id']);
        $tecnico = Tecnico::where('id',$id)
        ->update(['nombre'=>$args['nombre'],'apellido'=>$args['apellido'],'carnet_anverso'=>$args['carnet_anverso'],
        'email'=>$args['email'],'contrasenia'=>$args['contrasenia'],
        'foto'=>$args['foto']]);
        return $tecnico->all(); */
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
