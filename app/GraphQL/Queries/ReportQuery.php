<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Tecnico;
use App\Models\Calificacion;
use App\Models\Suscripcion;
use Illuminate\Support\Facades\DB;
use App\Models\Technician_subcripcion;
use App\Models\Tecnico_Habilidad;

class ReportQuery{
    public function reportSuscription($root, array $args) {
        $input = $args['Input'];

        $startDate = $input['startDate'] ?? null;
        $finishDate = $input['endDate'] ?? null;
        $type = $input['type'] ?? null;
        $state = $input['state'] ?? null ;
        $status = $input['status'] ?? false;

        // Consulta base
        $query = Technician_subcripcion::leftJoin('subcriptions', 'technician_subcription.subcriptionsId', '=', 'subcriptions.id')
                    ->leftJoin('technicians', 'technician_subcription.technicianId', '=', 'technicians.id');
        //dd($query->get());
        // Filtros dinámicos
        if ($startDate && $finishDate) {
            $query->where(function ($q) use ($startDate, $finishDate) {
                $q->whereDate('technician_subcription.starDateSubcription', '<=', $finishDate)
                ->whereDate('technician_subcription.endDateSubcription', '>=', $startDate);
            });
        }
        //dd($query->get());
        if ($type) {
            $query->where('technician_subcription.subcriptionsId', $type);
        }

        if (!is_null($state)) {
            //dd(1);
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
        // Si no es s ummary, devolvemos detalle
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
        $input = $args['Input'];
        //dd($input);
        $skills = $input['skills'] ?? null;
        $cities = $input['cities'] ?? null;
        $rates = $input['rates'] ?? null;
        $status = $input['status'] ?? null;
        //dd($skills,$cities,$rates,$status);
        $query = Tecnico_Habilidad::leftjoin('technicians','technicians.id','=','technician_skills.technicianId')
                                    ->leftjoin('skills','technician_skills.skillId','=','skills.id')
                                    ->leftjoin('cities','technicians.cityId','=','cities.id')
                                    ->distinct();
        if($skills){
            $query->where('technician_skills.skillId',$skills);
        }

        if($cities){
            $query->where('technicians.cityId',$cities);
        }

        if($status){
            $query->where('technicians.status',$status);
        }

        if($rates){
            switch ($rates){
                case 1:
                    $query->where('technicians.average_rating','>',1.00);
                break;
                case 2:
                    $query->where('technicians.average_rating','>',4.00);
                break;
                case 3:
                    $query->where('technicians.average_rating','<',3.00);
                break;
                case 4:
                    $query->where('technicians.average_rating','<',1.00);
                break;
            }
        }

        $content = $query->select(
            'technicians.id As id',
            DB::raw('CONCAT(COALESCE(technicians."firstName", \'\'), \' \', COALESCE(technicians."lastName", \'\')) AS "name_technician"'),
            'technicians.average_rating AS rates',
            'technicians.status AS status',
            'cities.name AS name_city',
            DB::raw('(
                SELECT STRING_AGG(skills.name, \', \')
                FROM technician_skills
                JOIN skills ON technician_skills."skillId" = skills.id
                WHERE technician_skills."technicianId" = technicians.id
            ) AS name_skill'),
            DB::raw('(SELECT COUNT(*) FROM requests WHERE requests."technicianId" = technicians.id) As count_requests'),
        )->get();
        //dd($content);
        return [
            'message' => 'Resulta de reporte True Technician',
            'techniciansData' => $content->map(function ($item) {
                return [
                    'id' => $item->id,
                    'name_technician' => $item->name_technician,
                    'rates' => $item->rates,
                    'status' => $item->status,
                    'name_city' => $item->name_city,
                    'name_skills' => $item->name_skill,
                ];
            })->toArray()
        ];
    }

    public function reportClient($root,array $args){
        $input = $args['Input'];

        $cities = $input['cities'] ?? null;
        $status = $input['status'] ?? null;
        $activity = $input['activity'] ?? null;

    }
}
