<?php

namespace App\Services;

class DiccionaryNotifications
{
    public static function get(){
        return [
            'new_message'=>[
                'title' => 'Nuevo mensaje',
                'body' => 'Tienes un nuevo mensaje.',
                'type' => 'mensaje',
                'type_users' => ['1', '2'],
            ],
        ];
    }

    public static function getRequest() {
        return [

            'request_sent' => [
                'title' => '¡Tu solicitud fue enviada!',
                'body' => 'Hemos enviado tu solicitud al técnico. Te avisaremos cuando la acepte.',
                'type' => 1,
                'status' => 2,
                'type_users' => ['1','2'],
            ],
            'request_accepted' => [
                'title' => '¡Solicitud aceptada! ✅',
                'body' => 'El técnico ha aceptado tu solicitud. Está programada para el día: ',
                'type' => 2, //esto pasa a hacer 2 y no 1
                'status' => 3,
                'type_users' => ['1','2'],
            ],
            'request_rejected' => [
                'title' => 'Solicitud rechazada ❌',
                'body' => 'Tu solicitud fue rechazada del día programado {fecha}. Puedes volver a intentarlo. ',
                'type' => 1,
                'status' => 4,
                'type_users' => ['1','2'],
            ],

        ];
    }

    public static function getService(){
        return
        [
            'services_anull_client' => [
                'title' => 'Servicio  anulado ❌',
                'body' => 'El cliente ha anulado el servicio programado para el día: ',
                'type' => 2,
                'status' => 5,
                'type_users' => ['1','2'],
            ],

            'services_anull_tech' => [
                'title' => 'Servicio anulado ❌',
                'body' => 'El tecnico ha anulado el servicio programado para el día: ',
                'type' => 2,
                'status' => 5,
                'type_users' => ['1','2'],
            ],

            'services_finish_tech' => [
                'title' => 'Servicio terminado 🔧',
                'body' => 'El técnico ',
                'type' => 2,
                'status' => 6,
                'type_users' => ['1','2'],
            ],

            'services_finish_client' => [
                'title' => 'Servicio completado 🔧',
                'body' => '¡El cliente marcado el servicio como finalizado!',
                'type' => 2,
                'status' => 7,
                'type_users' => ['1','2'],
            ],
        ];
    }

    public static function getSuscription(){
        return [
            'new_suscription' => [
                'title' => 'Suscripcion realizada.',
                'body' => 'El tecnico ha realizado la suscripcion.',
                'type' => 'suscripcion',
                'type_users' => ['1'],
            ],
            'renovation_suscription' => [
                'title' => 'Suscripcion renovada.',
                'body' => 'El tecnico ha renovado la suscripcion.',
                'type' => 'suscripcion',
                'type_users' => ['1'],
            ],
            'terminate_suscription' => [
                'title' => '🔒 Tu cuenta está por expirar',
                'body' => 'Tu suscripción vence el {fecha}. ¡Aún estás a tiempo de renovarla!',
                'type' => 'suscripcion',
                'type_users' => ['1'],
            ],
        ];
    }
    public static function getQualification(){
        return[
            'qualification_done' => [
                'title' => 'Calificacion realizada!! ✅.',
                'body' => 'El cliente califico el servicio!!.',
                'type' => 2,
                'status' => 6,
                'type_users' => ['1','2'],
            ],
            'qualification_c' => [
                'title' => '¿Qué te pareció el servicio?' ,
                'body' => '¡Califícanos! ⭐️' ,
            ],
        ];
    }

    public static function getUser(){
        return [
            'technician_password' => [
                'title' => 'Cambio de contraseña.',
                'body' => 'Su contraseña ha sido cambiada.',
                'type' => 'usuario',
                'type_users' => ['1'],
            ],

            'reset_subcription_tech'=>[
                'title' => 'Renovacion de suscripcion.',
                'body' => 'Su suscripcion ha sido renovada.',
                'type' => 'usuario',
                'type_users' => ['1'],
            ],

        ];
    }

    public static function getRecordAgenda(){
        return [
            'record_client'=>[
                'title'=>'¡Recordatorio de servicio! ⏰',
                'body'=>'Recuerda que pronto recibirás la visita del técnico.Para el : {fecha}',
                'type'=>6,
                'type_users'=>'[1,2]',
            ],

            'record_technician'=>[
                'title'=>'¡Recordatorio de servicio! 📅',
                'body'=>'Tienes una cita programada el : {fecha}. ¡Prepárate para asistir!',
                'type'=>6,
                'type_users'=>'[1,2]',
            ],
        ];
    }

    public static function getByKey(string $key)
    {
        $notifications = self::get();
        $request = self::getRequest();
        $service = self::getService();
        $suscription = self::getSuscription();
        $qualification = self::getQualification();
        $user = self::getUser();
        $record = self::getRecordAgenda();

        if (array_key_exists($key, $notifications)) {
            return $notifications[$key];
        } elseif (array_key_exists($key, $request)) {
            return $request[$key];
        } elseif (array_key_exists($key, $service)) {
            return $service[$key];
        } elseif (array_key_exists($key, $suscription)) {
            return $suscription[$key];
        } elseif (array_key_exists($key, $qualification)) {
            return $qualification[$key];
        } elseif (array_key_exists($key, $user)) {
            return $user[$key];
        } elseif (array_key_exists($key,$record)) {
            return $record[$key];
        }

        return null;
    }
}

