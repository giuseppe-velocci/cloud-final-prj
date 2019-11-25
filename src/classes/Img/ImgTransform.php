<?php
declare(strict_types=1);

namespace App\Img;

class ImgTransform {

    const MAX_HEIGHT = 250;

    /**
     * @$srcFilename string = fullpath for img to be processed
     */
    public function thumbnail(string $srcFilename, string $destsrcFilename, int $jpgQuality=75): void {
        // Get new dimensions so that height will be == MAX_HEIGHT
        list($width, $height) = getimagesize($srcFilename);
        $percent = $height / self::MAX_HEIGHT;

        $new_width = $width * $percent;
        $new_height = $height * $percent;

        // Resample
        $image_p = imagecreatetruecolor($new_width, $new_height);
        $image = imagecreatefromjpeg($srcFilename);
        if (! imagecopyresampled($image_p, $image, 0, 0, 0, 0, $new_width, $new_height, $width, $height)) {
            throw new Exception("Thumbnail creation for file: " . basename($srcFilename) . " faild.");
        }

        // Output
        if (! imagejpeg($image_p, $destsrcFilename, $jpgQuality)) {
            throw new Exception("Thumbnail for file: " . basename($srcFilename) . " could not be stored.");
        }
    }

}
