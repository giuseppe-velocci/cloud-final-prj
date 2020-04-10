<?php
declare(strict_types=1);

namespace App\Img;

class ImgTransform {

    const MAX_HEIGHT = 250;

    /**
     *  generate a thumbnail-specific filename (to ease their selection)
     */
    public static function getThumbnailName($srcFilename) {
        return 
        preg_replace_callback(
            '/\.\w+/', 
            function ($extension){
                return 'Thumb.jpg';
            },
            $srcFilename
        );
    }

    /**
     * get correct creation method based on file extension
     */
    protected static function createImage(string $filename) {
        $ext = pathinfo($filename, PATHINFO_EXTENSION);
        
        if ($ext == 'jpg' || $ext == 'jpeg') {
            return imagecreatefromjpeg($filename);
        } elseif ($ext == 'png') {
            return imagecreatefrompng ($filename);
        } elseif ($ext == 'gif') {
            return imagecreatefromgif ($filename);
        }

        return false;
    }

    /**
     * @$srcFilename string = fullpath for img to be processed
     */
    public static function thumbnail(string $srcFilename, string $destsrcFilename, int $jpgQuality=75): void {
        // Get new dimensions so that height will be == MAX_HEIGHT
        list($width, $height) = getimagesize($srcFilename);
        $percent = $height / self::MAX_HEIGHT;

        $new_width  = (int) floor($width / $percent);
        $new_height = (int) floor($height / $percent);

        // Resample
        $image_p = imagecreatetruecolor($new_width, $new_height);
        $image = self::createImage($srcFilename);
        if ($image === false && ! imagecopyresized($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height)) {
            throw new \Exception("Thumbnail creation for file: " . basename($srcFilename) . " faild.");
        }

        // Output
        if (! imagejpeg($image_p, $destsrcFilename, $jpgQuality)) {
            throw new \Exception("Thumbnail for file: " . basename($srcFilename) . " could not be stored.");
        }
    }

}
