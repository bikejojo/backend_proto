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
// ----- Parametros
        $searchParameter = $searchData['searchParameter'] ?? null;
        $skillId         = $searchData['skillsId'] ?? null;
        $experience      = $searchData['experience'] ?? null;
        $qualification   = $searchData['qualification'] ?? null; // Valor opcional

        $query = Tecnico::query();

        if (!empty($searchParameter)) {
            $query->leftJoin('technician_skills','technicianId','=','technicians.id')
                    ->leftJoin('skills','technician_skills.skillId','=','skills.id')
                    ->where(function ($query) use ($searchParameter){
                    $query->where('technicians.firstName','ILIKE',"%{$searchParameter}%")
                            ->orwhere('technicians.lastName','ILIKE',"%{$searchParameter}%");
                    })
                    ->orWhere('skills.name','ILIKE',"%{$searchParameter}%");
        }
        //dd($query->get());

        /*if(!empty($skillId)){
            $query->whereHas();
        }*/
/*
        // Consulta base
        $query = Tecnico::with(['technicianSkills' => function ($query) use ($skillId, $experience) {
            if ($skillId) {
                $query->where('skillId', $skillId);
            }
            if ($experience) {
                $query->where('experience', '<=', $experience);
            }
        }, 'technicianSkills.skill']);

        // Filtro por promedio de calificación si se proporciona
        if (!is_null($qualification)) {
            $query->where('average_rating', '<=', $qualification);
        }

        // Aplicar la condición whereHas para asegurar la relación
        if ($skillId || $experience) {
            $query->whereHas('technicianSkills', function ($query) use ($skillId, $experience) {
                if ($skillId) {
                    $query->where('skillId', $skillId);
                }
                if ($experience) {
                    $query->where('experience', '<=', $experience);
                }
            });
        }

        $technicians = $query->get();
        // Preparar la lista de técnicos y servicios con feedback
        $content = $technicians->map(function ($technician) use ($skillId) {
            // Filtrar habilidades específicas en los técnicos
            if ($skillId) {
                $technician->technicianSkills = $technician->technicianSkills->filter(function ($skill) use ($skillId) {
                    return $skill->skillId == $skillId;
                });
            }
            // Buscar el servicio y feedback relacionado al técnico
            // Buscar los servicios y feedback relacionados al técnico
                $feedbacks = Servicio::join('rating', 'services.id', '=', 'rating.serviceId')
                ->where('rating.technicialId', $technician->id) // Relacionar con el técnico actual
                ->select(
                    'services.id as id_service',
                    'services.titleService as titleService',
                    'services.serviceDescription as serviceDescription',
                    'rating.rating as rating',
                    'rating.feedback as feedback'
                )
                ->get();
            return [
                'technicians' => $technician,
                'feedback' => $feedbacks
            ];
        });
        return [
            'message' => 'Se encontraron los técnicos con sus servicios.',
            'content' => $content
        ];*/
    }
}
