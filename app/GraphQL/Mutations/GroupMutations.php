<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\helpers\ImageHelper;
use App\Models\Group;
use App\Models\Habilidad;
use App\Models\Skills_group;
use Carbon\Carbon;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\DB;
use Intervention\Image\Drivers\Gd\Driver;
use Intervention\Image\ImageManager;

class GroupMutations
{
    protected $app;
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function __construct()
    {
        $this->app = env('APP_URL');
    }

    public function create($root,array $args){
        $grupoData = $args['requestGroup'];

        $grupo = Group::create([
            'name' => $grupoData['name']
        ]);

        $grupoId = $grupo->id;
        $validators=ImageHelper::validationImageGroup($args);
        if ($validators->fails()) {
            return [
                'message' => 'Archivo de imagen inválido.'
            ];
        }
        ImageHelper::deleteDirectoryGrop($grupoId);
        $manager = new ImageManager(new Driver());
        $now = Carbon::now()->format('Ymd_His');
        $isPhotoGroup = (isset($args['photo']) && $args['photo'] instanceof UploadedFile);
        ImageHelper::existGroup($grupoId);
        if($isPhotoGroup){
            $photoGroupPath=ImageHelper::processImage($args['photo'],"/images/group/{$grupoId}/"."{$now}.png",$manager);
            $grupo->photo = $this->app . '/storage' . str_replace('/public','',$photoGroupPath);
            $grupo->save();
        }
        //return $grupo;
        return [
            'message' => 'Creacion correcta del grupo.!',
            'group'   => $grupo
        ];
    }

    public function update($root , array $args){
        $groupId = $args['id_group'];
        $group = Group::find($groupId);
        $validators = ImageHelper::validationImageGroup($args);
        if ($validators->fails()) {
            return [
                'message' => 'Archivo de imagen inválido.'
            ];
        }
        $now_ = Carbon::now()->format('Ymd_His');
        $groupId_ = $group->id;
        ImageHelper::deleteDirectoryGrop($groupId_);
        ImageHelper::existGroup($groupId_);
        $isPhotoGroup = isset($args['photo']) && $args['photo'] instanceof UploadedFile;

        $manager = new ImageManager(new Driver());
        if($isPhotoGroup){
            $groupPath = ImageHelper::processImage($args['photo'], "/images/group/{$groupId_}/"."{$now_}.png",$manager);

            $group->photo = $this->app . "/storage" . str_replace('/public','', $groupPath);
            $group->save();
        }
        return [
            'message' => 'Actualizacion correcta de grupo.!',
            'group' => $group
        ];

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
