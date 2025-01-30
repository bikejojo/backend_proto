<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Habilidad;
use App\Models\Tecnico;
use App\Models\Servicio;
use Illuminate\Support\Facades\DB;

class SkillQuery
{

    public function searchSkillTechnician($root,array $args){
        $technicianData=$args['requestSkillTechnician'];
        if(!empty($technicianData['searchParameter'])){
            $parameter=strtolower(trim($technicianData['searchParameter']));
            $skill=Habilidad::join('technician_skills','skills.id','=','technician_skills.skillId')
            ->join('technicians','technician_skills.technicianId','=','technicians.id')
            ->join('technician_subcription','technicians.id','=','technician_subcription.technicianId')
            ->where('technician_subcription.status',1)
            ->where(function ($query) use ($parameter){
                $query->where(DB::raw('LOWER("skills"."name")'),'LIKE',"%{$parameter}%");
            })
            ->select('technicians.*','technician_skills.experience','skills.name','skills.id')
            ->distinct()
            ->orderBy('technician_skills.experience','DESC')
            ->get();
        }else{
            $skill=Habilidad::join('technician_skills','skills.id','=','technician_skills.skillId')
            ->join('technicians','technician_skills.technicianId','=','technicians.id')
            ->join('technician_subcription','technicians.id','=','technician_subcription.technicianId')
            ->where('technician_subcription.status',1)
            ->select('technicians.*','technician_skills.experience','skills.name','skills.id')
            ->distinct()
            ->orderBy('technician_skills.experience','DESC')
            ->get();
        }

        return [
            'message'=>'Se encontro a los siguientes tecnicos.',
            'technicians' => $skill
        ];
    }


    public function searchFilterSkillTechnician($root, array $args){
        $searchData = $args['requestSkillTechnician'];

        // Parámetros asegurando que no sean arrays vacíos o valores no definidos
        $searchParameter = $searchData['searchParameter'] ?? null;
        $skillsIds       = (!empty($searchData['skillsId'])) ? $searchData['skillsId'] : null;
        $experience      = $searchData['experience'] ?? null;
        $qualification   = $searchData['qualification'] ?? null;
        $cityId          = $searchData['cityId'] ?? null;

        $query = Tecnico::query()
            ->select('technicians.*')
            ->leftJoin('technician_skills', 'technician_skills.technicianId', '=', 'technicians.id')
            ->leftJoin('skills', 'skills.id', '=', 'technician_skills.skillId')
            ->leftJoin('technician_subcription', function($join) {
                $join->on('technician_subcription.technicianId', '=', 'technicians.id')
                     ->where('technician_subcription.status', '=', 1); // Solo técnicos con suscripción activa
            });

        // 🔹 Filtro por nombre y apellido (si `searchParameter` tiene valor)
        if (!empty($searchParameter)) {
            $query->where(function ($q) use ($searchParameter) {
                $q->where('technicians.firstName', 'ILIKE', "%{$searchParameter}%")
                  ->orWhere('technicians.lastName', 'ILIKE', "%{$searchParameter}%");
            })
            ->orWhere('skills.name', 'ILIKE', "%{$searchParameter}%"); // Búsqueda por habilidad
        }

        // 🔹 Filtro por habilidades seleccionadas (si `skillsId` no está vacío)
        if (!empty($skillsIds)) {
            $query->whereIn('skills.id', $skillsIds);
        }

        // 🔹 Filtro por experiencia en habilidades (si `experience` tiene valor)
        if (!is_null($experience)) {
            $query->where('technician_skills.experience', '>=', $experience);
        }

        // 🔹 Filtro por calificación promedio (si `qualification` tiene valor)
        if (!is_null($qualification)) {
            $query->where('technicians.average_rating', '>=', $qualification);
        }

        // 🔹 Filtro por ciudad del cliente (si `cityId` tiene valor)
        if (!is_null($cityId)) {
            $query->where('technicians.cityId', '=', $cityId);
        }

        // Obtener los técnicos y sus habilidades relacionadas
        $technicians = $query->distinct()->get();

        // Formatear la respuesta
        $content = $technicians->map(function ($technician) {
            return [
                'id'         => $technician->id,
                'firstName'  => $technician->firstName,
                'lastName'   => $technician->lastName,
                'phoneNumber'=> $technician->phoneNumber,
                'photo'      => $technician->photo,
                'avg_rating' => $technician->average_rating,
                'skill'      => $technician->technicianSkills->map(function ($skill) {
                    return [
                        'id_skills'   => $skill->skillId,
                        'name'        => $skill->skill->name,
                        'experience'  => $skill->experience
                    ];
                })
            ];
        });

        return [
            'message' => 'Se encontraron los técnicos con sus habilidades.',
            'technicians' => $content
        ];
    }

}
