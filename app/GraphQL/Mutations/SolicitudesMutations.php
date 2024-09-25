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
        $solicitudData = $args['solicitud']['solicitudRequest'];
        $tecnico_id = $solicitudData['tecnico_id'];
        $cliente_id = $solicitudData['cliente_id'];
        $fecha_programada = $solicitudData['fecha_tiempo_registrado'];
        $fecha_carbon = Carbon::createFromFormat('Y-m-d', $fecha_programada);

        if ($fecha_carbon->isSameDay($fecha_programada)) {
            $fecha_hoy = Carbon::now();
        } else {
            return "fechas no coinciden";
        }
        //$fecha_inicio = $fecha_hoy;
        $fecha_fin = $fecha_hoy->addMinutes(5);

        // Crear instancia de Solicitud
        $solicitud = new Solicitud();
        $solicitud->cliente_id = $cliente_id;
        $solicitud->tecnico_id = $tecnico_id;
        $solicitud->fecha_tiempo_registrado = Carbon::now();
        $solicitud->fecha_tiempo_vencimiento = $fecha_fin;
        $solicitud->estado_id = 1;
        $solicitud->descripcion_servicio = $solicitudData['descripcion_servicio'];
        $solicitud->save();

        // Guardar detalles de la solicitud
        $detalles = new Solicitud_Detalle();
        $detalles->solicitud_id = $solicitud->id;
        $detalles->habilidades_solicitadas = json_encode($solicitudData['habilidades_solicitadas']); // Si es un array, asegúrate de guardarlo bien
        $detalles->save();

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
}
