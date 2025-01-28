<?php
namespace App\helpers;

use Illuminate\Contracts\Cache\Store;
use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;
use PhpParser\Node\Expr\NullsafeMethodCall;

class ImageHelper
{
    public static function validateImage($argumento){
        return Validator::make([
            'frontIdCard' => $argumento['frontIdCard'] ?? null ,
            'backIdCard'=> $argumento['backIdCard'] ?? null,
            'profile' => $argumento['photo'] ?? null ,
            ], [
                'frontIdCard' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
                'backIdCard' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp'
            ]);
    }
    public static function validateImagePhoto($argumento){
        return Validator::make([
            'profile' => $argumento['photo'] ?? null ,
            ], [
                'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp'
            ]);
    }

    public static function validateImagePublicity($argumento){
        return Validator::make([
            'logo' => $argumento['logo'] ?? null ,
            ], [
                'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp'
            ]);
    }

    public static function validateImageNotification($argumento){
        return Validator::make([
            'image' => $argumento['image'] ?? null ,
            ], [
                'image' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp'
            ]);
    }

    public static function validationImageSkill($argumento){
        return Validator::make([
            'photo' => $argumento['photo'] ?? null,
        ],[
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp'
        ]);
    }

    public static function validationImageGroup($argumento){
        return Validator::make([
            'photo' => $argumento['photo'] ?? null,
        ],[
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp'
        ]);
    }

    public static function validationImageSubGroup($argumento){
        return Validator::make([
            'photo' => $argumento['photo'] ?? null,
        ],[
            'photo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp'
        ]);
    }

    public static function createDirectorie($objetoId,$value){
        if($value == 1){
            Storage::makeDirectory('public/' . $objetoId . '/id_card');
            Storage::makeDirectory('public/' . $objetoId . '/profile');
        }
        if($value==2){
            Storage::makeDirectory('public/' . 'client_'.$objetoId . '/photo');
        }

        Storage::makeDirectory('public/publicidad/' . $objetoId . '/logo');

    }

    public static function createNotifications($objetoId){
        Storage::makeDirectory('public/notifications/'.$objetoId);
    }

    public static function createSkill($objetoId){
        Storage::makeDirectory('public/skill/'.$objetoId);
    }

    public static function createSubgroup($objetoId){
        Storage::makeDirectory('public/subgroup/'.$objetoId);
    }

    public static function createGroup($objetoId){
        Storage::makeDirectory('public/group/'.$objetoId);
    }

    public static function existSkill($objetoId){
        $directoryPath ='public/skill/'. $objetoId;
        if(!Storage::exists($directoryPath)){
            Storage::makeDirectory($directoryPath);
            return "Directorio 'skill' creado para el objeto: " . $objetoId;
        }
    }

    public static function existSubgroup($objetoId){
        $directoryPath ='public/subgroup/' . $objetoId;
        if(!Storage::exists($directoryPath)){
            Storage::makeDirectory($directoryPath);
            return "Directorio 'subgrupo' creado para el objeto: " . $objetoId;
        }
    }

    public static function existGroup($objetoId){
        $directoryPath ='public/group/' . $objetoId;
        if(!Storage::exists($directoryPath)){
            Storage::makeDirectory($directoryPath);
            return "Directorio 'grupo' creado para el objeto: " . $objetoId;
        }
    }

    public static function existDirectorie($objetoId){
        $directoryPath = 'public/'. $objetoId . '/profile';
        if (!Storage::exists($directoryPath)) {
            // Si no existe, lo crea
            Storage::makeDirectory($directoryPath);
            return "Directorio 'profile' creado para el objeto: " . $objetoId;
        }
    }

    public static function existDirectorieCard($objetoId){
        $directoryPath = 'public/'. $objetoId . '/id_card';

        if (!Storage::exists($directoryPath)) {
            // Si no existe, lo crea
            Storage::makeDirectory($directoryPath);
            return "Directorio 'id_card' creado para el objeto: " . $objetoId;
        }
    }

    public static function existDirectorieClient($objetoId){
        $directoryPath = 'public/'. 'client_'.$objetoId . '/photo';

        if (!Storage::exists($directoryPath)) {
            // Si no existe, lo crea
            Storage::makeDirectory($directoryPath);
            return "Directorio 'id_card' creado para el objeto: " . $objetoId;
        }
    }

    public static function deleteDirectoryIdCard($objetoId){
        Storage::deleteDirectory('public/' . $objetoId . '/id_card');
    }

    public static function deleteDirectoryProfile($objetoId){
        $files=Storage::allFiles('public/' . $objetoId . '/profile');
        Storage::delete($files);
    }

    public static function deleteDirectorySkill($objetoId){
        $files=Storage::allFiles('public/skill/'.$objetoId);
        Storage::delete($files);
    }
    public static function deleteDirectoryGrop($objetoId){
        $files=Storage::allFiles('public/grup/'.$objetoId);
        Storage::delete($files);
    }
    public static function processImage(UploadedFile $file, $path, $manager){
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
