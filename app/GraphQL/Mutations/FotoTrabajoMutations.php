<?php

namespace App\GraphQL\Mutations;

use App\Models\Foto_Trabajo;
use Illuminate\Support\Facades\Storage;
use Illuminate\http\UploadedFile;

use Intervention\Image\ImageManager;

use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Validator;

class FotoTrabajoMutations{
    public function create($root, array $args)
    {
        $fotoData = $args['fotoTrabajoRequest'];

        if (!is_array($args['fotos_url']) || count($args['fotos_url']) > 3) {
            throw new \Exception('Solo se pueden subir 3 fotos.');
        }

        // Validar que todos los archivos son imágenes y tienen el formato correcto
        foreach ($args['fotos_url'] as $foto) {
            $validator = Validator::make(['fotos_url' => $foto], [
                'fotos_url' => 'required|image|mimes:jpeg,png,jpg,gif,svg,webp',
            ]);

            if ($validator->fails()) {
                throw new \Exception('Archivo de imagen inválido');
            }
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

                $foto_trabajo = $tecnicoId . '/foto_trabajo/' . uniqid() . '.png';
                $fullPath = storage_path('app/public/' . $foto_trabajo);
                $image->save($fullPath, 75, 'png');

                // Usar str_replace para asegurarse de que se usen barras normales
                $url = str_replace('public/', '', $foto_trabajo);

                // Crear una nueva instancia de Foto_Trabajo para cada imagen
                $fotoTrabajo = new Foto_Trabajo([
                    'tecnicos_id' => $tecnicoId,
                    'fotos_url' => $url // Guardar cada URL por separado
                ]);
                $fotoTrabajo->save();

                // Guardar la URL en un array para referencia, si es necesario
                $fotoUrls[] = $url;
            }
        }

        // Puedes devolver las URLs guardadas, o cualquier otra cosa que necesites
        return [
            'urls' => $fotoUrls,
        ];
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
