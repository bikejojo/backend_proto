<?php

namespace App\GraphQL\Mutations;

use App\Models\Foto_Trabajo;
use Illuminate\Support\Facades\Storage;
use Illuminate\http\UploadedFile;

use Intervention\Image\ImageManager;

use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Validator;
use Psy\Readline\Hoa\Console;
use Symfony\Component\Console\Event\ConsoleEvent;

class FotoTrabajoMutations{
    public function create($root, array $args)
    {
        $fotoData = $args['fotoTrabajoRequest'];

        if (!is_array($args['fotos_url']) || count($args['fotos_url']) > 3) {
            throw new \Exception('Solo se pueden subir 3 fotos.');
        }

        $tecnicoId = $fotoData['tecnicos_id'];
        $manager = new ImageManager(new Driver());

        $fotoDir = 'public/' . $tecnicoId . '/foto_trabajo';
        Storage::makeDirectory($fotoDir);

        $fotoUrls = [];

        foreach ($args['fotos_url'] as $foto) {
            if ($foto instanceof UploadedFile) {
                $image = $manager->read($foto->getRealPath());
                $image->resize(700, null, function ($constrain) {
                    $constrain->aspectRatio();
                    $constrain->upsize();
                });
                $array = array('\\',"//","\/","/public","\/");
                $foto_trabajo = $tecnicoId . '/foto_trabajo/' . uniqid() . '.png';
                $fullPath = storage_path('app/public/' . $foto_trabajo);
                $image->save($fullPath, 75, 'png');
                $url = preg_replace('/\\\\|\/\/|\/public/', '/', $foto_trabajo);
                //$url = stripslashes($foto_trabajo);
                // Crear una nueva instancia de Foto_Trabajo para cada imagen
                //dd($foto_trabajo);
                $fotoTrabajo = new Foto_Trabajo([
                    'tecnicos_id' => $tecnicoId,
                    'descripcion' => $fotoData['descripcion'],
                    'fotos_url' =>  $url,
                ]);
                //$fotoTrabajo->fotos_url = preg_replace('/\\\\|\/\/|\/public/', '/',$fotoTrabajo->fotos_url);
                //dd($fotoTrabajo->fotos_url);
                $fotoTrabajo->save();

                // Guardar la URL en un array para referencia, si es necesario
                $fotoUrls[] = $fotoTrabajo;
            }
        }
        //dd($foto_trabajo);
        return $fotoTrabajo;
        // Puedes devolver las URLs guardadas, o cualquier otra cosa que necesites
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
