<?php

namespace App\GraphQL\Queries;

use App\Models\Cliente_Interno;
use App\Models\Notification;
use App\Models\NotificationUser;
use App\Models\NotificationsDevice;
use App\Models\Tecnico;
use App\Models\User;
use App\Services\ValidationModels;
use Illuminate\Support\Facades\DB;

class NotificationsQuery
{
    /** @param  array{}  $args */

    public function getNotificationsTech($root,array $args){
        try {
            $input = $args['Input'];
            $userId = $input['userTech'];
            $technician=Tecnico::where('id',$userId)->first();
            $user = User::find($technician->userId);

            if(!$user){
                return [
                    'message'=>'No se encontró el usuario para el dispositivo especificado.',
                    'status' => '2'
                ];
            }

            $notificacionMandaste = Notification::select('id','title', 'body', 'data')
                                ->where('sender_id', $user->id)
                                ->orderBy('id','ASC')
                                ->get()
                                ->map(function ($item) {
                                    $data = $item->data;

                                    return [
                                        'id' => $item->id,
                                        'title' => $item->title,
                                        'body' => $item->body,
                                        'data'  => [
                                            'typeNotification'      => $data['typeNotification'] ?? null,
                                            'full_name_tech'             => $data['full_name_tech'] ?? null ,
                                            'photo_tech'                 => $data['photo_tech'] ?? null,
                                            'rate_tech'                  => $data['rate_tech'] ?? null ,
                                            'actividad'             => $data['actividad'] ?? null,
                                            'ubicacion'             => $data['ubicacion'] ?? null,
                                            'referencia_ubicacion'  => $data['referencia_ubicacion'] ?? null,
                                            'estado_del_servicio'   => $data['estado_del_servicio'] ?? null,
                                            'id_client'             => $data['id_client'] ?? null,
                                            'id_technician'         => $data['id_technician'] ?? null,
                                            'id_service'            => isset($data['id_service']) ? $data['id_service'] : 'no existe',
                                            'id_request'            => $data['id_request'] ?? null ,
                                            'description'           => $data['description'] ?? null ,
                                            'date_request'          => isset($data['date_request']) ? $data['date_request'] : 'no existe',
                                            'date_service'          => isset($data['date_service']) ? $data['date_service'] : 'no existe',
                                        ],
                                        'type' => 1
                                    ];
                                });

            $notificationRecibidad = NotificationUser::join('notifications', 'notifications_user.notification_id', '=', 'notifications.id')
                                    ->where('notifications_user.user_id', $user->id)
                                    ->select('notifications.id','notifications.title', 'notifications.body', 'notifications.data')
                                    ->orderBy('notifications.id','ASC')
                                    ->get()
                                    ->map(function ($item) {
                                        $data = json_decode($item->data, true);
                                        return [
                                            'id' => $item->id,
                                            'title' => $item->title,
                                            'body' => $item->body,
                                            'data'  => [
                                                'typeNotification'      => $data['typeNotification'] ?? null,
                                                'full_name'             => $data['full_name'] ?? null,
                                                'photo'                 => $data['photo'] ?? null,
                                                'rate'                  => $data['rate'] ?? null,
                                                'actividad'             => $data['actividad'] ?? null,
                                                'ubicacion'             => $data['ubicacion'] ?? null,
                                                'referencia_ubicacion'  => $data['referencia_ubicacion'] ?? null,
                                                'estado_del_servicio'   => $data['estado_del_servicio'] ?? null,
                                                'id_client'             => $data['id_client'] ?? null,
                                                'id_technician'         => $data['id_technician'] ?? null,
                                                'id_service'            => isset($data['id_service']) ? $data['id_service'] : 'no existe',
                                                'id_request'            => $data['id_request'] ?? null ,
                                                'description'           => $data['description'] ?? null ,
                                                'date_request'          => isset($data['date_request']) ? $data['date_request'] : 'no existe',
                                                'date_service'          => isset($data['date_service']) ? $data['date_service'] : 'no existe',
                                            ],
                                            'type' => 2
                                        ];
                                    });
            //7dd($notificationRecibidad);
            $allNotifications = $notificationRecibidad->merge($notificacionMandaste)->values();

            return [
                'message' => 'Notificaciones para el usuario: ' . $technician->firstName .' '. $technician->lastName,
                'notifications' => $allNotifications,
                'status' => '1'
            ];

        } catch (\Exception $e){
            return [
                'message'=>'Las siguientes fallas son estasssss: ' . $e->getMessage(),
                'status' => '3'
            ];
        }
    }

    public function getNotificationsClient($root,array $args){
        try {
            $input = $args['Input'];
            $userId = $input['userClient'];
            $client=Cliente_Interno::where('id',$userId)->first();
            $user = User::find($client->userId);

            if(!$user){
                return [
                    'message'=>'No se encontró el usuario para el dispositivo especificado.',
                    'status' => '2'
                ];
            }

            $notificacionMandaste = Notification::select('id','title', 'body', 'data')
                        ->where('sender_id', $user->id)
                        ->get()
                        ->map(function ($item) {
                            $data =  $item->data;
                            return [
                                'id' => $item->id,
                                'title' => $item->title,
                                'body'  => $item->body,
                                'data'  => [
                                    'typeNotification'      => $data['typeNotification'] ?? null,
                                    'full_name'             => $data['full_name'] ?? null,
                                    'photo'                 => $data['photo'] ?? null,
                                    'rate'                  => $data['rate'] ?? null,
                                    'actividad'             => $data['actividad'] ?? null,
                                    'ubicacion'             => $data['ubicacion'] ?? null,
                                    'referencia_ubicacion'  => $data['referencia_ubicacion'] ?? null,
                                    'estado_del_servicio'   => $data['estado_del_servicio'] ?? null,
                                    'id_client'             => $data['id_client'] ?? null,
                                    'id_technician'         => $data['id_technician'] ?? null,
                                    'id_service' => isset($data['id_service']) ? $data['id_service'] : 'no existe',
                                    'id_request'            => $data['id_request'] ?? null ,
                                    'description'           => $data['description'] ?? null ,
                                    'date_request'          => isset($data['date_request']) ? $data['date_request'] : 'no existe',
                                    'date_service'          => isset($data['date_service']) ? $data['date_service'] : 'no existe',
                                ],
                                'type' => 1
                            ];
                        });

            $notificationRecibidad = NotificationUser::leftJoin('notifications', 'notifications_user.notification_id', '=', 'notifications.id')
                            ->where('notifications_user.user_id', $user->id)
                            ->select('notifications.id','notifications.title', 'notifications.body', 'notifications.data')
                            ->get()
                            ->map(function ($item) {
                                $data = json_decode($item->data, true) ;
                                return [
                                    'id' => $item->id,
                                    'title' => $item->title,
                                    'body'  => $item->body,
                                    'data'  => [
                                        'typeNotification'      => $data['typeNotification'] ?? null,
                                        'full_name'             => $data['full_name'] ?? null,
                                        'photo'                 => $data['photo'] ?? null,
                                        'rate'                  => $data['rate'] ?? null,
                                        'actividad'             => $data['actividad'] ?? null,
                                        'ubicacion'             => $data['ubicacion'] ?? null,
                                        'referencia_ubicacion'  => $data['referencia_ubicacion'] ?? null,
                                        'estado_del_servicio'   => $data['estado_del_servicio'] ?? null,
                                        'id_client'             => $data['id_client'] ?? null,
                                        'id_technician'         => $data['id_technician'] ?? null,
                                        'id_service'            => isset($data['id_service']) ? $data['id_service'] : 'no existe',
                                        'id_request'            => $data['id_request'] ?? null ,
                                        'description'           => $data['description'] ?? null ,
                                        'date_request'          => isset($data['date_request']) ? $data['date_request'] : 'no existe',
                                        'date_service'          => isset($data['date_service']) ? $data['date_service'] : 'no existe',
                                    ],
                                    'type' => 2
                                ];
                            });
            //$allNotifications = $notificationRecibidad->merge($notificacionMandaste)->values();
            $allNotifications = collect($notificationRecibidad)->merge(collect($notificacionMandaste))->values();


            return [
                'message' => 'Notificaciones para el usuario: ' . $client->firstName .' '. $client->lastName,
                'notification' => $allNotifications,
                'status' => '1'
            ];

        } catch (\Exception $e){
            return [
                'message'=>'Las siguientes fallas son estasssss: ' . $e->getMessage(),
                'status' => '3'
            ];
        }

    }
}
