<?php
namespace App\Service;

use Spatie\Image\Enums\Fit;
use Spatie\Image\Image;
use Symfony\Component\HttpFoundation\File\File;

class ImageCompressor{

    public function compress(string $path){
        $image = Image::load($path);
        if (!isset($image->exif()['FileSize'])) {
            $file = new File($path);
            $size = $file->getSize();
        }else{
             $size = $image->exif()['FileSize'];
        }
        $quality = $this->getQualityFromSize($size);
        $image->quality($quality)
        ->fit(Fit::Max,800)
        ->optimize()
        ->save($path);
    }

    private function getQualityFromSize(int $size){
        $quality = 83;
        $qualityBySizes = [
            4194304 => 40,
            3145728 => 50,
            2097152 => 60,
            1048576 => 80
        ];
    
        foreach ($qualityBySizes as $bytes => $value) {
            if ($size > $bytes ) {
            return $value;
            }
        }
        return $quality;
    }

    public function createThumbnail(string $path, string $thumbPath, int $width = 397, int $quality = 72): void
    {
        $dir = dirname($thumbPath);
        if (!is_dir($dir)) {
            mkdir($dir, 0755, true);
        }

        Image::load($path)
            ->fit(Fit::Max, $width)
            ->quality($quality)
            ->save($thumbPath);
    }
}