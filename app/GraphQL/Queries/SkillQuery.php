<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Habilidad;
use App\Models\Tecnico;
use App\Models\Cliente_Interno;
use Illuminate\Support\Facades\DB;

class SkillQuery
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function searchSkillTechnician($root,array $args){
        $technicianData=$args['requestSkillTechnician'];
        if(!empty($technicianData['searchParameter'])){
            $parameter=strtolower(trim($technicianData['searchParameter']));
            $skill=Habilidad::join('technician_skills','skills.id','=','technician_skills.skillId')
            ->join('technicians','technician_skills.technicianId','=','technicians.id')
            ->where(function ($query) use ($parameter){
                $query->where(DB::raw('LOWER("skills"."name")'),'LIKE',"%{$parameter}%");
            })
            ->select('technicians.*','technician_skills.experience','skills.*')
            ->orderBy('technician_skills.experience','DESC')
            ->get();
        }else{
            $skill=Habilidad::join('technician_skills','skills.id','=','technician_skills.skillId')
            ->join('technicians','technician_skills.technicianId','=','technicians.id')
            ->select('technicians.*','technician_skills.experience','skills.*')
            ->orderBy('technician_skills.experience','DESC')
            ->get();
        }
        //dd($skill);
        return [
            'message'=>'Se encontro a los siguientes tecnicos.',
            'technicians' => $skill
        ];
    }

    public function searchFilterSkillTechnician($root, array $args)
    {
        $searchData = $args['requestSkillTechnician'];
        $skillId = $searchData['skillsId'] ?? null;
        $experience = $searchData['experience'] ?? null;
        if(!empty($searchData['experience'])){
            $technicians = Tecnico::with(['technicianSkills' => function ($query) use ($skillId, $experience) {
                $query->where('skillId', $skillId)->where('experience', '<=', $experience); 
            }, 'technicianSkills.skill'])
                ->whereHas('technicianSkills', function ($query) use ($skillId, $experience) {
                    $query->where('skillId', $skillId)
                        ->where('experience', '<=', $experience);
                })
                ->get();

            $filteredTechnicians = $technicians->map(function ($technician) use ($skillId) {
                $technician->technicianSkills = $technician->technicianSkills->filter(function ($skill) use ($skillId) {
                    return $skill->skillId == $skillId;
                });

                return $technician;
            });
        }else{
            $technicians = Tecnico::with(['technicianSkills' => function ($query) use ($skillId) {
                $query->where('skillId', $skillId); 
            }, 'technicianSkills.skill'])
                ->whereHas('technicianSkills', function ($query) use ($skillId) {
                    $query->where('skillId', $skillId);
                })
                ->get();

            $filteredTechnicians = $technicians->map(function ($technician) use ($skillId) {
                $technician->technicianSkills = $technician->technicianSkills->filter(function ($skill) use ($skillId) {
                    return $skill->skillId == $skillId;
                });

                return $technician;
            });
        }
        return [
            'message'=>'Se encontro a los siguientes tecnicos.',
            'technicians' => $filteredTechnicians
        ];
    }

}
