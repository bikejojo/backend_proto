<?php

namespace App\Services;

use App\Models\Solicitud;
use App\Models\Servicio;
use App\Models\Estado; // Asegúrate de que este es el modelo correcto para la tabla de estados

class StatusAssigner{
    // Constantes para los estados de solicitud
    const REQUEST_PENDING = 'pendiente por aceptar';
    const REQUEST_REJECTED = 'rechazado por tecnico';
    const REQUEST_ACCEPTED = 'aceptado';

    // Constantes para los estados de servicio
    const SERVICE_PENDING = 'pendiente';
    const SERVICE_COMPLETED = 'terminado';
    
}
