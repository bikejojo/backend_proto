<?php

namespace App\GraphQL\Mutations;

use App\Models\Solicitud;
use App\Models\Foto_Solicitud;
use App\Models\Solicitud_Detalle;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

use Carbon\Carbon;

class SolicitudesMutations
{
    public function create($root , array $args){
        // Extraer datos del request dentro de solicitudRequest
        $solicitudData = $args['solicitud']['solicitudRequest'];
        $hoy= Carbon::today();
        $fecha_programada = $solicitudData['fecha_tiempo_registrado'];
        //validar fecha programada
        if(!$this->validarFechaProgramada($fecha_programada)){
            return $this->errorResponse('Fechas No Coinciden');
        }
        #cambiar el timepo de fecha fin;
        $solicitud = new Solicitud();
        $fecha_hoy = Carbon::now();
        $fecha_fin = $fecha_hoy->addMinutes(10);
        // Crear instancia de Solicitud
        $solicitud->cliente_id = $solicitudData['cliente_id'];
        $solicitud->tecnico_id = $solicitudData['tecnico_id'];
        $solicitud->fecha_tiempo_registrado = Carbon::now();
        $solicitud->fecha_tiempo_actualizado = Carbon::now();
        $solicitud->fecha_tiempo_vencimiento = $fecha_fin;
        $solicitud->estado_id = 1;

        $descripcion_servicio = trim($solicitudData['descripcion_servicio']);
        $latitud = trim($solicitudData['latitud']);
        $longitud = trim($solicitudData['longitud']);
        $descripcion_servicio = trim($solicitudData['descripcion_ubicacion']);

        $solicitud->descripcion_servicio = $descripcion_servicio;
        $solicitud->latitud = $latitud;
        $solicitud->longitud = $longitud;
        $solicitud->descripcion_ubicacion = $descripcion_servicio;
        $solicitud->save();
        // Guardar detalles de la solicitud
        $habilidades_ids = $solicitudData['habilidades_solicitadas'];
        foreach($habilidades_ids as $habilidad_id){
            $detalles = new Solicitud_Detalle();
            $detalles->solicitud_id = $solicitud->id;
            $detalles->habilidades_solicitadas = $habilidad_id;
            $detalles->save();
        }
        // Crear directorio para fotos
        $solicitudDir = 'public/' . $solicitudData['cliente_id'];;
        Storage::makeDirectory($solicitudDir . '/foto_solicitud');
        // Manejar las fotos
        $fotoUrls = [];
        $manager = new ImageManager(new Driver());
        $contador = 0;
        foreach ($args['solicitud']['fotos_url'] as $foto) {
            if ($foto instanceof UploadedFile) {
                // Procesar la imagen
                $contador = $contador + 1;
                $image = $manager->read($foto->getRealPath());
                $image->resize(800, 800, function ($constrain) {
                    $constrain->aspectRatio();
                    $constrain->upsize();
                });
                $foto_trabajo = $solicitudData['cliente_id'] . '/foto_solicitud/' . 'solicitud'. $contador . '.png';
                $fullPath = storage_path('app/public/' . $foto_trabajo);
                $image->save($fullPath, 75, 'png');
                // Guardar la URL en la base de datos
                $foto = new Foto_Solicitud();
                $foto->solicitud_id = $solicitud->id;
                $foto->descripcion = $solicitudData['descripcion_servicio']; // Misma descripción para todas las fotos
                $foto->fotos_url = preg_replace('/\\\\|\/\/|\/public/', '/', $foto_trabajo)?preg_replace('/\\\\|\/\/|\/public/', '/', $foto_trabajo):null;
                $foto->save();
                $fotoUrls[] = $foto->fotos_url;
            }
        }
        return [
            'message' => 'solicitud Creada',
            'solicitud' => Solicitud::where('id', $solicitud->id)->with('solicituds')->first()
        ];
   }
    public function modifyState($root,array $args){
        $solicitudData = $args['solicitudRequest'];

        $tecnico_id = $solicitudData['tecnico_id'];
        $cliente_id = $solicitudData['cliente_id'];
        $solicitud_id = $solicitudData['solicitud_id'];
        $solicitudActual = new Solicitud();
        $solicitudActual = Solicitud::where('tecnico_id',$tecnico_id)
                        ->where('cliente_id',$cliente_id)
                        ->where('id',$solicitud_id)
                        ->first();
        if($solicitudActual){
            switch($solicitudData['estado_id']){
                case 3:
                    $solicitudActual->estado_id = 3;
                    $solicitudActual->fecha_tiempo_actualizado = Carbon::now();
                    break;
                case 5:
                    $solicitudActual->fecha_tiempo_actualizado = Carbon::now();
                    $solicitudActual->estado_id = 5;
                    break;
                case 6:
                    $solicitudActual->fecha_tiempo_actualizado = Carbon::now();
                    $solicitudActual->estado_id = 6;
                    break;
                default:
                    return $this->errorResponse("El estado proporcionado no es válido");
            }
            #$solicitudActual->fecha_tiempo_vencimiento = Carbon::now();
            $solicitudActual->save();
            return [
                'message'=> 'actualizacion de solicitud hecha ',
                'solicitud'=> $solicitudActual
            ];
        }else{
            return [
                'message' => 'solcitud no encontrada',
                'solictud' => $solicitudActual
            ];
        }
    }

    private function errorResponse($message){
        return [
            'message' => $message,
            'solicitud' => null
        ];
    }

    private function validarFechaProgramada($fecha_programada){
        $fecha_carbon =Carbon::createFromFormat('Y-m-d',$fecha_programada);
        return $fecha_carbon->isSameDay($fecha_programada);
    }
}
