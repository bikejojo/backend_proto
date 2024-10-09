<?php

namespace App\GraphQL\Mutations;

use App\Models\Certificacion;
use Illuminate\http\UploadedFile;

use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class CertificacionMutations{
    public function create($root,array $args){

        $certificadoData = $args['certificacionRequest'];
        $certificado = Certificacion::create($certificadoData);

        if ($this->validaImage($args)->fails()){
            throw new \Exception('Archivo de imagen inválido');
        }

        $certificadoId= $certificado->tecnico_id;

        $manager = new ImageManager(new Driver());

        $certificadoDir = 'public/' . $certificadoId.'/certificados';
        Storage::makeDirectory($certificadoDir);

        if(isset($args['foto_url'])&&$args['foto_url'] instanceof UploadedFile){
            $image = $manager->read($args['foto_url']->getRealPath());
            $image->resize(750,750,function($constrain){
                $constrain->aspectRatio();
                $constrain->upsize();
            });
            $certificacion =$certificadoId. '/certificados'.'/'. 'certificado' . '.png';
            $fullPath = storage_path('app/public/'.$certificacion);
            $image->save($fullPath,78,'png');
            $certificado->foto_url = str_replace('public/','',$certificacion);
        }
        $certificado->save();
        return $certificado;
    }
    public function update($root,array $args){
        $id=Certificacion::find($args['id']);
        $certificacion=Certificacion::where('id',$id)
        ->update(['nombre'=>$args['nombre'],'fecha_certificacion'=>$args['fecha_certificacion'],'foto_url'=>$args['foto_url']]);
        return $certificacion;
    }
    private function validaImage($args){
        return validator::make($args, [
            'foto_url' =>  'required|file|mimes:jpeg,png,jpg,gif,svg,webp',
        ]);
    }
}
