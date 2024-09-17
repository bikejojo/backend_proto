<?php

namespace App\GraphQL\Mutations;

use App\Models\Certificacion;
use Illuminate\http\UploadedFile;
use Intervention\image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;

class CertificacionMutations{
    public function create($root,array $args){
        $certificadoData = $args['certificadoRequest'];
        $certificado = Certificacion::create($certificadoData);
        $certificadoId= $certificado->tecnico_id;

        $manager = new ImageManager(new Driver());

        $certificadoDir = 'public/' . $certificadoId;
        Storage::makeDirectory($certificadoDir.'certificado');

        if(isset($args['foto_url'])&&$args['foto_url'] instanceof UploadedFile){
            $image = $manager->read($args['foto_url']->getRealPath());
            $image->resize(700,null,function($constrain){
                $constrain->aspectRatio();
                $constrain->upsize();
            });
            $certificacion =$certificadoId. '/certificado'.'/'. uniqid() . '.png';
            $fullPath = storage_path('app/public/'.$certificacion);
            $image->save($fullPath,75,'png');
            $certificado->foto_url = str_replace('public/','',$certificacion);

        }
    }
    public function update($root,array $args){
        $id=Certificacion::find($args['id']);
        $certificacion=Certificacion::where('id',$id)
        ->update(['nombre'=>$args['nombre'],'fecha_certificacion'=>$args['fecha_certificacion'],'foto_url'=>$args['foto_url']]);
    }
    public function delete($root,array $args){

    }

}
