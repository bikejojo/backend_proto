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
                        'photo' => $skillGroup->skill->photo
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
        try {
            $groups = Group::join('group_subgroups','group.id','=','group_subgroups.groupId')
                            ->join('sub_groups','group_subgroups.subGroupId','=','sub_groups.id')
                            ->join('sub_groups_skill','sub_groups.id','=','sub_groups_skill.subGroupId')
                            ->join('skills','sub_groups_skill.skillId','=','skills.id')
                            ->select(
                                'group.id As group_id',
                                'group.name As group_name',
                                'sub_groups.id As subGroups_id',
                                'sub_groups.description As subGroups_description',
                                'skills.id As skill_id',
                                'skills.name As skill_name',
                            )
                            ->get();
            $groupedData = $groups->groupBy('group_id')->map(function ($groupItems) {
                return [
                    'id' => $groupItems->first()->group_id,
                    'name' => $groupItems->first()->group_name,
                    'subGrups' => $groupItems->groupBy('subGroups_id')->map(function ($subGroupItems) {
                        return [
                            'id' => $subGroupItems->first()->subGroups_id,
                            'description' => $subGroupItems->first()->subGroups_description,
                            'skill' => $subGroupItems->map(function ($skill) {
                                return [
                                    'id' => $skill->skill_id,
                                    'name' => $skill->skill_name
                                ];
                            })->toArray()
                        ];
                    })->values()->toArray()
                ];
            })->values()->toArray();

            return [
                'message' => 'Lista de grupos obtenida con éxito.',
                'responde' => $groupedData
            ];
        } catch (\Exception $e){
            return [
                'message' => 'Error en la consulta: ' . $e->getMessage(),
                'responde' => []
            ];
        }
    }
}
