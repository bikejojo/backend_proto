<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Group;
use App\Models\Group_Subgroup;
use App\Models\Skills_group;
use App\Models\Habilidad;

class GroupSkillQuery
{


    public function allGroupSkill($root,array $args){
        $groups = Group::all();

        return[
            'message' => 'Categorias de habilidades!.',
            'category' => $groups
        ];
    }

    public function allGroupSkill_($root, array $args)
    {
        $groups = Group::with('skillGroup.skill')->get();
        //dd($groups);
        if ($groups->isEmpty()) {
            return [
                'message' => 'No se encontraron grupos.',
                'category' => [],
            ];
        }

        $result = $groups->map(function ($group) {
            return [
                'id' => $group->id,
                'nameCategory' => $group->name,
                'photo' => $group->photo,
                'skill' => $group->skillGroup->map(function ($skillGroup) {
                    return [
                        'id' => $skillGroup->skill->id ?? null,
                        'name' => $skillGroup->skill->name ?? null,
                    ];
                })->filter(), // Elimina elementos nulos
            ];
        });

        return [
            'message' => 'Categorías obtenidas con éxito.',
            'category' => $result,
        ];
    }

    public function gropSubSkill ($root,array $args){
        /*$group = Group::join('group_subgroups','group_subgroups.groupId','=','group.id')
                        ->join('sub_groups','group_subgroups.subGroupId','=','sub_groups.id')
                        ->join('sub_groups_skill','sub_groups.id','=','sub_groups_skill.subGroupId')
                        ->join('skills','sub_groups_skill.skillId','=','skills.id');*/
        $group = Group::select('group.name','group,id')
                        ->get();
        $groupIds = Group::pluck('id');
        $subGroups = Group_Subgroup::whereIn('groupId', $groupIds)
                    ->join('sub_groups','group_subgroups.subGroupId','=','sub_groups.id')
                    ->select('id', 'name', 'description')
                    ->get();
    }
}
