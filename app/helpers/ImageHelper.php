<?php
namespace App\Helpers;

use Intervention\Image\ImageManager;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\UploadedFile;


class ImageHelper
{
    public static function validateImage($argumento){
        return Validator::make([
            'frontIdCard' => $argumento['frontIdCard'] ?? null ,
            'backIdCard'=> $argumento['backIdCard'] ?? null ,
            'photo' => $argumento['photo'] ?? null ,
            ], [
                'frontIdCard' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp',
                'backIdCard' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg,webp'
            ]);
    }
    public static function validateImagePhoto($argumento){
        return Validator::make([
            'photo' => $argumento['photo'] ?? null ,
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

    public static function createDirectorie($objetoId,$value){
        if($value == 1){
            Storage::makeDirectory('public/' . $objetoId . '/id_card');
            Storage::makeDirectory('public/' . $objetoId . '/photo');
        }
        if($value==2){
            Storage::makeDirectory('public/' . 'client_'.$objetoId . '/photo');
        }

        Storage::makeDirectory('public/publicidad/' . $objetoId . '/logo');

    }
    public static function deleteDirectoryIdCard($objetoId){
        Storage::deleteDirectory('public/' . $objetoId . '/id_card');
    }
    public static function deleteDirectoryProfile($objetoId){
        $files=Storage::allFiles('public/' . $objetoId . '/profile');
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
