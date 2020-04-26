<?php
declare(strict_types=1);

namespace App\Helper;

class ExifDataHelper {

    public static function exifString2Array(string $exifString) {
        $exifString = str_replace(['\n', '\r', '\rn', '\\', '"'], '', $exifString);
        $exifData = preg_split('/({|}|\[|\])/', $exifString);
        $exifData = array_filter($exifData, function($item) {
            return strlen($item) > 0;
        });
        array_walk($exifData, function(&$item) { 
            if (! ctype_alpha (substr($item, 0, 1))) {
                $item = substr($item, 1);
            }
        });

        return $exifData;
    }
}