<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Publicidad;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;
use App\Helpers\ImageHelper;
use App\Services\StateCatalog;
use App\Services\ValidationModels;

final class PublicityMutations{

    protected $app;

    public function __construct()
    {
        $this->app = env('APP_URL').':'.env('SERVER_PORT');
    }

    public function create($root,array $args){
        $publicityDate = $args['requestPublicity'];
        DB::beginTransaction();
        try{
            $validators = ImageHelper::validateImage($args);
            if ($validators->fails()) {
                return [
                    'message' => 'Archivo de imagen inválido.'
                ];
            }

            $publicity = Publicidad::create([
                'descriptionPublicity' => $publicityDate['descriptionPublicity'],
                'commercialName' =>       $publicityDate['commercialName'],
                'link'=>                  $publicityDate['link'],
                'createdDate'=>           $publicityDate['createdDate'],
                'startDate' =>            $publicityDate['startDate'],
                'finishDate' =>           $publicityDate['finishDate'],
                'categoryId' =>           $publicityDate['id_category'],
                'status'=>                StateCatalog::STATUS_PUBLICITY_ACTIVE
            ]);

            $publicityId = $publicity->id;
            $publicityComplete = $publicityId;
            $value=0;
            ImageHelper::createDirectorie($publicityComplete,$value);
            $now = Carbon::now()->format('Ymd_His');
            $manager = new ImageManager(new Driver());
            if (isset($args['logo']) && $args['logo'] instanceof UploadedFile) {
                $frontIdPath = ImageHelper::processImage($args['logo'], "/publicidad/{$publicityComplete}/logo/". "{$now}.png", $manager);
                $publicity->logo =$this->app . '/storage' . str_replace('public/', '', $frontIdPath);
            }
            $publicity->save();
            DB::commit();
            return [
                'message' => 'datos de publicidad',
                'publicity' => $publicity
            ];
        }catch(\Exception $e){
            DB::rollback();
            return [
                'La falla es la siguiente: ' => $e->getMessage()
            ];
        }
    }
    public function updateExpiration($root , array $args){
        $publicityDate = $args['requestPublicity'];
        $publicityId = $publicityDate['id_publicity'];
        $publicity = Publicidad::find($publicityId);
        if (!$publicity) {
            return [
                'message' => 'No se encontró la publicidad con el ID proporcionado.'
            ];
        }
        if($publicityDate['status'] === StateCatalog::STATUS_PUBLICITY_EXPIRATION ){
            $publicity->status = StateCatalog::STATUS_PUBLICITY_EXPIRATION;
        }
        $publicity->save();
        return [
            'message' => 'Publicidad expirada.',
            'publicity' => $publicity
        ];
    }

    public function update($root,array $args){
        $publicityDate = $args['requestPublicity'];
        $publicityId = $args['id'];
        $publicity = Publicidad::find($publicityId);
        $publicityIdOld = $publicity->id;
        $publicityCompleteOld = $publicityIdOld;

        if (!$publicity) {
            return [
                'message' => 'No se encontró la publicidad con el ID proporcionado.'
            ];
        }
        DB::beginTransaction();
        try{
            ########################################################
            $publicity->descriptionPublicity = $publicityDate['descriptionPublicity'];
            $publicity->commercialName = $publicityDate['commercialName'];
            $publicity->link = $publicityDate['link'];
            $publicity->startDate = $publicityDate['startDate'];
            $publicity->createdDate = $publicityDate['createdDate'];
            $publicity->finishDate = $publicityDate['finishDate'];
            $publicity->categoryId = $publicityDate['id_category'];
            $publicity->save();
            #######################################################
            $publicityId = $publicity->id;
            $publicityComplete =  $publicityId;
            $isLogoPublicity = isset($args['logo']) && $args['logo'] instanceof UploadedFile;
            $manager = new ImageManager(new Driver());
            if ($publicityCompleteOld !== $publicityComplete) {
                Storage::deleteDirectory('public/publicidad/'.$publicityCompleteOld);
                $this->createDirectories($publicityComplete);
            }

            // Verifica si hay un logo para procesar
            if ($isLogoPublicity) {
                // Elimina el logo anterior
                Storage::disk('public')->delete($publicity->logo);

                // Procesa y guarda la nueva imagen en el nuevo directorio
                $now = Carbon::now()->format('Ymd_His');
                $logoPath = ImageHelper::processImage($args['logo'], "/publicidad/{$publicityComplete}/logo/"."{$now}.png", $manager);
                $publicity->logo =$this->app . '/storage' . str_replace('public/', '', $logoPath);
                $publicity->save();
            }
            DB::commit();
            return [
                'message' => 'Publicidad actualizada',
                'publicity' => $publicity
            ];
        } catch(\Exception $e){
            DB::rollback();
            return [
                'La falla es la siguiente: ' => $e->getMessage()
            ];
        }
    }
    public function delete($root,array $args){
        $publicityId = $args['requestPublicity']['id'];
        $publicity = ValidationModels::validationPublicity($publicityId); /*Publicidad::find($publicityId);
        if (!$publicity) {
            return [
                'message' => 'No se encontró la publicidad'
            ];
        }*/
        // Realizar baja lógica
        $publicity->status = StateCatalog::STATUS_PUBLICITY_CANCELED;
        $publicity->save();
        return [
            'message' => 'La publicidad se dio de baja.',
            'publicity' => $publicity
        ];
    }

    private function createDirectories($publicityId){
        Storage::makeDirectory('public/publicidad/' . $publicityId . '/logo');
    }
}
