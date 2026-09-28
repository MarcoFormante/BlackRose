<?php
namespace App\Service;

use Spatie\Image\Enums\Fit;
use Spatie\Image\Image;

class ImageCompressor{

    public function compress(string $path){
        $image = Image::load($path);
        $size = $image->exif()['FileSize'];
        $quality = $this->getQualityFromSize($size);
        $image->quality($quality)
        ->fit(Fit::Max,800)
        ->optimize()
        ->save($path);
    }

    private function getQualityFromSize(int $size){
        $quality = 70;
        $qualityBySizes = [
            4194304 => 40,
            3145728 => 50,
            2097152 => 60
        ];
    
        foreach ($qualityBySizes as $bytes => $value) {
            if ($size > $bytes ) {
            return $value;
            }
        }
        return $quality;
    }
}