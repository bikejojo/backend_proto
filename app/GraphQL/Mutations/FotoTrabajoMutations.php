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
        $fotos = Foto_Trabajo::create($fotoData);
        $validator = validator::make($args, [
            'foto_url' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
        ]);
        if ($validator->fails())
            throw new \Exception('Archivo de imagen inválido');
        $fotoId=$fotos->tecnicos_id;
        $manager = new ImageManager(new Driver());
        $fotoDir= 'public/' . $fotoId.'/foto_trabajo';
        Storage::makeDirectory($fotoDir);
        if(isset($args['fotos_url'])&&$args['fotos_url'] instanceof UploadedFile){
            $image = $manager->read($args['fotos_url']->getRealPath());
            $image->resize(700,null,function($constrain){
                $constrain->aspectRatio();
                $constrain->upsize();
            });
            $foto_trabajo =$fotoId. '/foto_trabajo'.'/'. uniqid() . '.png';
            $fullPath = storage_path('app/public/'.$foto_trabajo);
            $image->save($fullPath,75,'png');
            $fotos->fotos_url = str_replace('public/','',$foto_trabajo);

        }
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
