<?php

namespace App\GraphQL\Mutations;

use App\Models\Habilidad;
use App\Models\Tecnico_Habilidad;
use Illuminate\Support\Facades\DB;
use App\helpers\ImageHelper;
use App\Models\Skills_group;
use Carbon\Carbon;
use Illuminate\Database\Capsule\Manager;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Log;
use Intervention\Image\ImageManager;
use Intervention\Image\Drivers\Gd\Driver;

class HabilidadMutations {

   protected $app;
   public function __construct()
   {
        $this->app = config('app.url');;
   }

   public function create($root,array $args){
        $groupId=$args['groupId'];
        $validators = ImageHelper::validationImageSkill($args);
        if ($validators->fails()) {
            return [
                'message' => 'Archivo de imagen inválido.'
            ];
        }
        DB::beginTransaction();
        try {

            $habilidad = new Habilidad();
                $habilidad->name = $args['name'];
                $habilidad->icons = 'hammer-wrench';
                $habilidad->status = 1;
                $habilidad->save();

            $habilidadId = $habilidad->id;
            ImageHelper::createSkill($habilidadId);
            $manager = new ImageManager(new Driver);
            $_now = Carbon::now()->format('Ymd_His');
            if(isset($args['photo']) && $args['photo'] instanceof UploadedFile ){
                $photoPath = ImageHelper::processImage($args['photo'],"/images/subgroup/{$habilidadId}/"."{$_now}.png",$manager);
                $habilidad->photo = $this->app . '/storage' . str_replace('public/','',$photoPath);
                $habilidad->save();
            }

            Skills_group::create([
                'groupId' => $groupId,
                'skillsId' => $habilidad->id
            ]);


            DB::commit();
            return [
                'message' => 'creacion de habilidad',
                'skill' => $habilidad
            ];
        } catch (\Exception $e) {
            DB::rollBack();
            return[
                'message' => 'Se produjo el siguiente error ' . $e->getMessage()
            ];
        }
   }
   public function update($root,array $args){
    DB::beginTransaction();
    try {
        $idSkill= $args['id'];
        $idGruop=$args['groupId'];
        $relationGroup = Skills_group::where('skillsId',$idSkill)->first();
        $habilidad = Habilidad::where('id',$idSkill)->first();//update(['name'=>$args['name']]);
        $habilidad->name = $args['name'];
        $habilidad->icons = 'hammer-wrench';
        $habilidad->save();
        $relationGroup->groupId = $idGruop;
        $relationGroup->save();
        $habilidadId = $habilidad->id;
        ImageHelper::deleteDirectorySkill($habilidadId);
        $manager = new ImageManager(new Driver());
        $now = Carbon::now()->format('Ymd_His');
        $isPhotoHabilidad = isset($args['photo']) && $args['photo'] instanceof UploadedFile;
        ImageHelper::existSkill($habilidadId);
        if($isPhotoHabilidad){
            $photoHabilidadPath = ImageHelper::processImage($args['photo'],"/skill/{$habilidadId}/"."{$now}.png",$manager);
            $habilidad->photo=$this->app . '/storage'. str_replace('/public','',$photoHabilidadPath);
        }
        $habilidad->save();
        DB::commit();
        return [
            'message' => 'Actualizacion de habilidad',
            'skill' => $habilidad
        ];
    } catch (\Exception $err) {
        DB::rollBack();
        return [
            'message' => 'Error al actualizar la habilidad: ' . $err->getMessage()
        ];
    }
    /*$habilidad_ = Habilidad::where('id',$id)->update(['name'=>$args['name']]);
    $habilidad = Habilidad::where('id',$id)->first();
    $habilidadId = $habilidad->id;
    ImageHelper::deleteDirectorySkill($habilidadId);
    $manager = new ImageManager(new Driver());
    $now = Carbon::now()->format('Ymd_His');
    $isPhotoHabilidad = isset($args['photo']) && $args['photo'] instanceof UploadedFile;
    ImageHelper::existSkill($habilidadId);
    if($isPhotoHabilidad){
        $photoHabilidadPath = ImageHelper::processImage($args['photo'],"/skill/{$habilidadId}/"."{$now}.png",$manager);
        $habilidad->photo=$this->app . '/storage'. str_replace('/public','',$photoHabilidadPath);
    }
    $habilidad->save();
    return [
        'message' => 'Actualizacion de habilidad',
        'skill' => $habilidad
    ];*/
   }
   public function delete($root,array $args){
    try{
        $skill = Habilidad::where('id',$args['id'])->first();
            $skill->status = 0;
            $skill->save();
        if(!$skill){
            return ['message'=> 'Borrado no existoso'];
        }else{
            return ['message'=> 'Borrado existoso'];
        }
    }catch(\Exception $e){
        Log::info('Se presentaron los siguientes problemas: ',$e->getMessage());
    }
   }
}
