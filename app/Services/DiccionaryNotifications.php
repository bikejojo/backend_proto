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

            'request_send' => [
                'title' => '¡Tienes una nueva solicitud de servicio! 📬',
                'body' => 'Has recibido una nueva solicitud. Revisa los detalles y confirma tu disponibilidad. 🛠️',
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
                'body' => 'Tu solicitud fue rechazada. Puedes volver a intentarlo. ',
                'type' => 1,
                'status' => 4,
                'type_users' => ['1','2'],
            ],
            'request_rejected_system' => [
                'title' => 'Solicitud expirada 🕒',
                'body' => 'Tu solicitud programada para el dia {fecha} ha expirado por falta de actividad. ¡No te preocupes! Puedes solicitar de nuevo. 🚀',
                'type' => 1,
                'status' => 8,
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
                'body' => 'El técnico ha anulado el servicio programado para el día: ',
                'type' => 2,
                'status' => 5,
                'type_users' => ['1','2'],
            ],

            'serv_anull_client' => [
                'title' => 'Servicio anulado 💡',
                'body' => 'Tu servicio fue cancelado exitosamente. ¡Te esperamos cuando necesites agendar otro servicio!',
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
                'body' => '¡El cliente concluyo con el proceso del servicio!',
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
                'title' => '¡Renovación confirmada!',
                'body' => '¡Hola {nombre}! Tu suscripción ha sido renovada. ¡Gracias por seguir con nosotros! 🎉',
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
                'type' => 2,
                'status' => 6,
                'type_users' => ['1','2'],
            ],
        ];
    }

    public static function getUser(){
        return [
            'technician_password' => [
                'title' => '¡Cambio exitoso de contraseña! 🔑',
                'body' => 'Tu contraseña se actualizó correctamente.',
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
                'body'=>'Tienes una cita programada el : {fecha}. ¡Prepárate para asistir!',
                'type'=>6,
                'type_users'=>'[1,2]',
            ],

            'record_technician'=>[
                'title'=>'¡Recordatorio de servicio! 📅',
                'body'=>'Recuerda que pronto recibirás la visita del técnico. Para el: {fecha}',
                'type'=>6,
                'type_users'=>'[1,2]',
            ],
        ];
    }

    public static function getPublicity(){
        return [
            'send_publicity'=>[
                'title'=> '¡Publicidad exclusivas para técnicos! 🎯',
                'body'=>'',
                'type'=>4,
                'status'=> 2,
                'type_users'=>'[1]',
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
        $publicity = self::getPublicity();

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
        } elseif (array_key_exists($key,$publicity)) {
            return $publicity[$key];
        }

        return null;
    }
}

