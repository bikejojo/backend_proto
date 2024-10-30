<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Publicidad;

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
}
