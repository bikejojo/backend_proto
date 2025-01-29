<?php declare(strict_types=1);

namespace App\GraphQL\Queries;

use App\Models\Group;
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
}
