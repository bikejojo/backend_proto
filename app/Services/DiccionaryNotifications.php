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
                'title' => 'Su solicitud fue enviada',
                'body' => 'La solicitud fue enviada al tecnico.',
                'type' => 'solicitud enviada',
                'type_users' => ['1','2'],
            ],
            'request_accepted' => [
                'title' => 'Solicitud aceptada por el tecnico.',
                'body' => 'Su solicitud esta agendada.',
                'type' => 'solicitud Aceptada',
                'type_users' => ['1','2'],
            ],
            'request_rejected' => [
                'title' => 'Solicitud rechazada por el tecnico.',
                'body' => 'Se rechazo la solicitud del cliente.',
                'type' => 'solicitud Rechaza',
                'type_users' => ['1','2'],
            ],

        ];
    }

    public static function getService(){
        return
        [
            'services_anull_client' => [
                'title' => 'Servicio anulado por el cliente.',
                'body' => 'El cliente ha anulado el servicio.',
                'type' => 'servicio',
                'type_users' => ['1','2'],
            ],

            'services_anull_tech' => [
                'title' => 'Servicio anulado por el tecnico.',
                'body' => 'El tecnico ha anulado el servicio.',
                'type' => 'servicio',
                'type_users' => ['1','2'],
            ],

            'services_finish_tech' => [
                'title' => 'El servicio a sido finalizado por el tecnico.',
                'body' => 'El tecnico dio como finalizado el servicio.',
                'type' => 'servicio',
                'type_users' => ['1','2'],
            ],

            'services_finish_client' => [
                'title' => 'El servicio ha sido finalizado por el cliente.',
                'body' => 'El cliente dio como finalizado el servicio.',
                'type' => 'servicio',
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
                'title' => 'Suscripcion terminada.',
                'body' => 'La suscripcion ha terminado.',
                'type' => 'suscripcion',
                'type_users' => ['1'],
            ],
        ];
    }
    public static function getQualification(){
        return[
            'qualification_done' => [
                'title' => 'Calificacion realizada.',
                'body' => 'El cliente ha calificado el servicio.',
                'type' => 'servicio',
                'type_users' => ['1','2'],
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


    public static function getByKey(string $key)
    {
        $notifications = self::get();
        $request = self::getRequest();
        $service = self::getService();
        $suscription = self::getSuscription();
        $qualification = self::getQualification();
        $user = self::getUser();

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
        }

        return null;
    }
}

