<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Cliente_Externo;
use App\Models\Cliente_Interno;
use App\Models\Tecnico;
use App\Models\Contacto;
use Carbon\Carbon;

class ContactoMutations{
    public function create($root , array $args ){
        $contactData = $args;
        $technicalId = $contactData['techinicalId'];
        $technical = Tecnico::find($technicalId);
        if(!$technical){
            // retornar que el registro de tecnico es null o no esxiste
            return null;
        }
        $clientId = $contactData['clientId'];
        $clientInternalId=Cliente_Interno::find($clientId);
        $clientExternoid=Cliente_Externo::find($clientId);

        if($clientInternalId == null && $clientExternoid == null){
            // retornar que el registro de tecnico es null o no esxiste
            return null;
        }
        $contactData['issue'] = trim($contactData['issue']);
        $contactData['typeContact'] = trim($contactData['typeContact']);
        $fechaActual = Carbon::now();
        $fechaFormateada = $fechaActual->format('d-m-Y');
        $contactData['dateRegistered'] = $fechaFormateada;
        $contacto = Contacto::create($contactData);
        return [
            'message' => 'Registro de contacto exitoso!',
            'contact' => $contacto ,

        ];
    }
    public function update($root , array $args ){
    }
    public function delete($root , array $args ){
    }
    public function list($root , array $args){
        $contact = $args['contactRequest'];
        $tecnicoId = $contact['tecnicoId'];
        $list = Contacto::where('technicianId',$tecnicoId)->get();

        if($list->isEmpity()){
            return [
                'message' => 'No tiene contenido en su agenda' ,
                'contact' => null
            ];
        }
        return [
            'message' => 'Listado de Agenda del tecnico' ,
            'contact' => $list
        ];
    }
}
