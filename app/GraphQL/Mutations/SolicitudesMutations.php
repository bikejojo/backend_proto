<?php

namespace App\GraphQL\Mutations;

use App\Models\Solicitud;
use App\Models\Foto_Solicitud;
use App\Models\Solicitud_Detalle;
use Illuminate\Support\Facades\Storage;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Validator;
use Intervention\Image\Drivers\Gd\Driver;

use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager;

class SolicitudesMutations
{
    public function create($root , array $args){
        // Extraer datos del request dentro de solicitudRequest
        $solicitud = new Solicitud();
        $solicitudData = $args['solicitud']['solicitudRequest'];
        $hoy= Carbon::today();

        $tecnico_id = $solicitudData['tecnico_id'];
        $cliente_id = $solicitudData['cliente_id'];

        $numSolicitud= Solicitud::where('cliente_id',$cliente_id)
        ->whereDate('fecha_tiempo_registrado',$hoy)
        ->count();

        if ($numSolicitud >= 3) {
            return ['message'=>'No puedes enviar más de 3 solicitudes por día.',
                    'solicitud'=> null ];
        }
        
        $fecha_programada = $solicitudData['fecha_tiempo_registrado'];
        $fecha_carbon = Carbon::createFromFormat('Y-m-d', $fecha_programada);
        if ($fecha_carbon->isSameDay($fecha_programada)) {
            $fecha_hoy = Carbon::now();
        } else {
            return ['message'=>'fechas no coinciden',
                    'solicitud'=> null
                ];
        }
        $fecha_fin = $fecha_hoy->addMinutes(2);
        // Crear instancia de Solicitud

        $solicitud->cliente_id = $cliente_id;
        $solicitud->tecnico_id = $tecnico_id;
        $solicitud->fecha_tiempo_registrado = Carbon::now();
        $solicitud->fecha_tiempo_vencimiento = $fecha_fin;
        $solicitud->estado_id = 1;
        $solicitud->descripcion_servicio = $solicitudData['descripcion_servicio'];
        $solicitud->latitud = $solicitudData['latitud'];
        $solicitud->longitud = $solicitudData['longitud'];
        $solicitud->descripcion_ubicacion = $solicitudData['descripcion_ubicacion'];
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
        $solicitudDir = 'public/' . $tecnico_id;
        Storage::makeDirectory($solicitudDir . '/foto_solicitud');
        // Manejar las fotos
        $fotoUrls = [];
        $manager = new ImageManager(new Driver());
        foreach ($args['solicitud']['fotos_url'] as $foto) {
            if ($foto instanceof UploadedFile) {
                // Procesar la imagen
                $image = $manager->read($foto->getRealPath());
                $image->resize(700, null, function ($constrain) {
                    $constrain->aspectRatio();
                    $constrain->upsize();
                });
                $foto_trabajo = $tecnico_id . '/foto_solicitud/' . uniqid() . '.png';
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
        return Solicitud::where('id', $solicitud->id)->with('solicituds')->first();
    }

    public function technicianModify($root,array $args){
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
                case 2:
                    $solicitudActual->estado_id = 2;
                    break;
                case 4:
                    $solicitudActual->estado_id = 4;
                    break;
                default:
                    return response()->json(['message' => 'El estado proporcionado no es válido.'], 400);
            }
            $solicitudActual->fecha_tiempo_vencimiento = Carbon::now();
            $solicitudActual->save();
            return $solicitudActual;
            //return response()->json(['message' => 'Solicitud actualizada con éxito.', 'solicitud' => $solicitudActual], 200);
        }else{
            return $solicitudActual;
            //return response()->json(['message' => 'No se encontró una solicitud pendiente con el ID proporcionado.'], 404);
        }
    }
}
