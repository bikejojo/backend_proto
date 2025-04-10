<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Tecnico;
use App\Models\Calificacion;
use App\Models\Suscripcion;
use Illuminate\Support\Facades\DB;
use App\Models\Technician_subcripcion;

class ReportQuery{
    public function reportSuscription($root, array $args) {
        $input = $args['Input'];

        $startDate = $input['startDate'] ?? null;
        $finishDate = $input['endDate'] ?? null;
        $type = $input['type'] ?? null;
        $state = $input['state'] ?? null;
        $status = $input['status'] ?? false;

        // Consulta base
        $query = Technician_subcripcion::leftJoin('subcriptions', 'technician_subcription.subcriptionsId', '=', 'subcriptions.id')
                    ->leftJoin('technicians', 'technician_subcription.technicianId', '=', 'technicians.id');

        // Filtros dinámicos
        if ($startDate && $finishDate) {
            $query->whereDate('technician_subcription.starDateSubcription', '>=', $startDate)
                  ->whereDate('technician_subcription.endDateSubcription', '<=', $finishDate);
        }

        if ($type) {
            $query->where('technician_subcription.subcriptionsId', $type);
        }

        if ($state) {
            $query->where('technician_subcription.status', $state);
        }

        if (!$status) {
            // Agrupación sin detalles, solo conteo por suscripción
            $summary = Technician_subcripcion::leftJoin('subcriptions', 'technician_subcription.subcriptionsId', '=', 'subcriptions.id')
                            ->select(
                                'subcriptions.name AS name_sub',
                                'subcriptions.status AS state_sub',
                                DB::raw('COUNT(technician_subcription."technicianId") AS technician_count')
                            )
                            ->groupBy('subcriptions.name', 'subcriptions.status')
                            ->get();

            return [
                'message' => 'Resulta de reporte True',
                'content' => $summary->map(function ($item) {
                    return [
                        'name_subs' => $item->name_sub,
                        'state_sub' => $item->state_sub,
                        'count_sub' => $item->technician_count
                    ];
                })->toArray()
            ];
        }

        // Si no es summary, devolvemos detalle
        $content = $query->select(
                        'subcriptions.name AS name_sub',
                        DB::raw('CONCAT(COALESCE(technicians."firstName", \'\'), \' \', COALESCE(technicians."lastName", \'\')) AS "fullName_technician"'),
                        'technician_subcription.status AS state_TechSub',
                        'technician_subcription.starDateSubcription AS startSub',
                        'technician_subcription.endDateSubcription AS endSub'
                    )
                    ->get();
            //dd($content);
        $grouped = $content->groupBy('name_sub');

        return [
            'message' => 'Resulta de reporte True',
            'technicianData' => $grouped->map(function ($items, $nameSub) {
                return [
                    'name_sub' => $nameSub,
                    'type_report' => $items->map(function ($item) {
                        return [
                            'fullName_technician' => $item->fullName_technician,
                            'state_TechSub' => $item->state_TechSub,
                            'startSub' => $item->startSub,
                            'endSub' => $item->endSub,
                        ];
                    })
                ];
            })->toArray()
        ];
    }


    public function reportTechnician($root,array $args){
        $startDate = $args['input']['startDate'] ?? null;
        $finishDate = $args['input']['finishDate'] ?? null;
        $type = $args['input']['type'] ?? null;
        $satus = $args['input']['status'] ?? null;
    }

    public function reportClient($root,array $args){
        $startDate = $args['input']['startDate'] ?? null;
        $finishDate = $args['input']['finishDate'] ?? null;
        $type = $args['input']['type'] ?? null;
        $satus = $args['input']['status'] ?? null;
    }
}
