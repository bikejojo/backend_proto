<?php

namespace App\GraphQL\Mutations;

use App\Models\Foto_Trabajo;
use Illuminate\Support\Facades\Storage;
use Illuminate\http\UploadedFile;
use Intervention\image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Validator;

class FotoTrabajoMutations{
    public function create($root,array $args){
        //return $foto = Foto_Trabajo::create($args);
        $fotoData = $args['fotoRequest'];
        //validacion de cantidad de fotos
        if (!is_array($args['foto_url']) || count($args['foto_url']) > 3) {
            throw new \Exception('Solo se pueden subir 3 fotos.');
        }
        // Validar que todos los archivos son imágenes y tienen el formato correcto
        foreach ($args['foto_url'] as $foto) {
            $validator = Validator::make(['foto_url' => $foto], [
                'foto_url' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
            ]);

            if ($validator->fails()) {
                throw new \Exception('Archivo de imagen inválido');
            }
        }
        $tecnicoId = $fotoData['tecnicos_id'];
        $fotoDir= 'public/' . $tecnicoId.'/foto_trabajo';
        Storage::makeDirectory($fotoDir);
        //$fotos = Foto_Trabajo::create($fotoData);
        $manager = new ImageManager(new Driver());
        $fotoUrls = [];
        foreach($args['foto_url'] as $foto) {
            if($foto instanceof UploadedFile){
                $image = $manager->read($foto->getRealPath());
                $image->resize(700,null,function($constrain){
                    $constrain->aspectRatio();
                    $constrain->upsize();
                });
                $foto_trabajo =$tecnicoId. '/foto_trabajo'.'/'. uniqid() . '.png';
                $fullPath = storage_path('app/public/'.$foto_trabajo);
                $image->save($fullPath,75,'png');
                $fotoUrls[] = str_replace('public/','',$foto_trabajo);
            }
        }
        $foto->fotos_url = json_encode($fotoUrls);
        $foto->save();

        return $foto;
    }
    public function update($root,array $args){
        $id=Foto_Trabajo::find($args['id']);
        $foto_trabajo = Foto_Trabajo::where('id',$id)->update(['foto'=>$args['foto'],'irl_foto'=>$args['url_foto']]);
    }
    public function delete($root,array $args){
        $id=Foto_Trabajo::find($args['id']);
        if($id){
            return ['message' => 'borrado no exitoso'];
        }else{
            Foto_Trabajo::where('id',$id)->delete();
            return ['message' => 'borrado exitoso'];
        }
    }

}
