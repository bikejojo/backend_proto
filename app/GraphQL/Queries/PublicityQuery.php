<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Publicidad;
use Illuminate\Support\Carbon;

final readonly class PublicityQuery
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function getAllowPublicity($root,array $args){
        $publicidad = Publicidad::all();
        return [
            'message' => 'Se esta devolviendo todas las publicaciones.',
            'publicity' => $publicidad
        ];
    }

    public function getIdPublicity($root,array $args){
        $publicidadDataId = $args['id'];
        if(isset($publicidadData)){
            return [
                'message' => 'No existe la publicidad publicidad.'
            ];
        }
        $publicidad = Publicidad::find($publicidadDataId);
        if(!is_null($publicidad)){
            return [
                'message' => 'Retorno de la publicidad solicitada.',
                'publicity' => $publicidad
            ];
        }else{
            return [
                'message' => 'La publicidad esta retornado null.',
                'publicity' => $publicidad
            ];
        }
    }

    public function getDateExpiration($root , array $args){
        $public  = Publicidad::orderBy('finishDate','ASC')->get();
        //dd($public);
        return [
            'message' => 'Listado de publicidad con fecha de expiracion',
            'publicity' => $public
        ];
    }

    public function getStatusPublicity($root,array $args){
        $publicData = $args['requestPublicity'];
        $id_status = $publicData['id_status'];
        //dd($id_status);
        $query = Publicidad::orderBy('finishDate','ASC');
        if (!is_null($id_status) && in_array($id_status, [0, 1, 2])) {
            $query->where('status', $id_status);
        }
        $publicity = $query->get();
        if($publicity->isEmpty()){
            return [
                'message' => 'No existen servicios en esta actividad.'
            ];
        }
        return [
            'message' => 'Listado de publicidad por estado',
            'publicity' => $publicity
        ];
    }

    public function getCategoryPublicity($root,array $args){
        $publicData = $args['requestPublicity'];
        //dd($publicData);
        $id_category = $publicData['id_category'];
        //dd($id_status);
        $now=Carbon::now();
        $query = Publicidad::where('finishDate', '>=' ,$now);
        if (!is_null($id_category) && in_array($id_category, [1, 2,3,4])) {
            $query->where('categoryId', $id_category);
        }
        $publicity = $query->get();
        if($publicity->isEmpty()){
            return [
                'message' => 'No existen servicios en esta actividad.'
            ];
        }
        return [
            'message' => 'Listado de publicidad por categoria',
            'publicity' => $publicity
        ];
    }
}
