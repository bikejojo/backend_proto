<?php declare(strict_types=1);

namespace App\GraphQL\Mutations;

use App\Models\Publicidad;
use Illuminate\Http\UploadedFile;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Intervention\Image\Drivers\Gd\Driver;

final readonly class PublicityMutations
{
    /** @param  array{}  $args */
    public function __invoke(null $_, array $args)
    {
        // TODO implement the resolver
    }

    public function create($root,array $args){
        $publicityDate = $args['requestPublicity'];
        $validators = $this->validateImage($args);
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
            'finishDate' =>           $publicityDate['finishDate'],
            'status'=>                1
        ]);
        $publicityId = $publicity->id;

        $this->createDirectories($publicityId);
        $manager = new ImageManager(new Driver());
        if (isset($args['logo']) && $args['logo'] instanceof UploadedFile) {
            $frontIdPath = $this->processImage($args['logo'], "/publicity_{$publicityId}/front.png", $manager);
            $publicity->logo = str_replace('public/', '', $frontIdPath);
        }
        $publicity->save();

        return [
            'message' => 'datos de publicidad',
            'publicity' => $publicity
        ];
    }
    public function update($root,array $args){
        $publicityDate = $args['requestPublicity'];
        $publicityId = $publicityDate['id_publicity'];
        $publicity = Publicidad::find($publicityId);
        if (!$publicity) {
            return [
                'message' => 'No se encontró la publicidad con el ID proporcionado.'
            ];
        }
            $publicity->descriptionPublicity = $publicityDate['descriptionPublicity'];
            $publicity->commercialName = $publicityDate['commercialName'];
            $publicity->link = $publicityDate['link'];
            $publicity->createdDate = $publicityDate['createdDate'];
            $publicity->finishDate = $publicityDate['finishDate'];
        $publicity->save();

        return [
            'message' => 'Publicidad actualizada',
            'publicity' => $publicity
        ];
    }
    public function delete($root,array $args){
        $publicityDate = $args['requestPublicity'];
        $publicityId = $publicityDate['id'];
        $publicity = Publicidad::find($publicityId);
        // Verificar si la publicidad existe
        if (!$publicity) {
            return [
                'message' => 'No se encontró la publicidad'
            ];
        }
        // Realizar baja lógica
        $publicity->status = 0;
        $publicity->save();
        return [
            'message' => 'La publicidad se dio de baja.',
            'publicity' => $publicity
        ];
    }

    private function validateImage($args){
        return Validator::make([
        'logo' => $args['logo'] ?? null ,
        ], [
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
        ]);
    }

    private function createDirectories($publicityId){
        Storage::makeDirectory('public/' . $publicityId . '/logo');
    }

    private function processImage(UploadedFile $file, $path, $manager){
        $image = $manager->read($file->getRealPath());
        $image->resize(750, 750, function ($constraint) {
            $constraint->aspectRatio();
            $constraint->upsize();
        });
        $fullPath = storage_path("app/public/{$path}");
        $image->save($fullPath, 80, 'png');
        return $path;
    }
}
