<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Group;
use App\Models\Habilidad;
use App\Models\Skills_group;
use Illuminate\Support\Facades\DB;

class GroupMutations
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function create($root,array $args){
        $grupoData = $args['requestGroup'];

        $grupo = Group::create([
            'name' => $grupoData['name']
        ]);
        return $grupo;
    }

    public function grupHabilidad($root,array $args){
        $grupoData = $args['requestGroup'];
        $grupo = Group::find($grupoData['groupId']);
        if(!$grupo){
            return[
                'message'=>'No  existe la categoria para el servicio!.'
            ];
        }
        $skill = Habilidad::find($grupoData['skillId']);
        if(!$skill){
            return [
                'message'=>'No existe la habilidad.'
            ];
        }
        DB::beginTransaction();
        try{
            $groupSkill = Skills_group::create([
                'groupId'=>$grupo->id,
                'skillsId'=> $skill->id
            ]);
            DB::commit();
            return [
                'message' => 'Se creo con existo la union con la habilidad a la categoria.',
                'grupoSkill'=> $groupSkill
            ];
        } catch(\Exception $e){
            DB::rollBack();
            return [];
        }
    }
}
