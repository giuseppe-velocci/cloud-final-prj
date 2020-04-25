<?php
declare(strict_types=1);

namespace App\Helper;

class ExifDataPrint {

    public static function printExif($exif) {
        $data = print_r($exif, true);
        $data = str_replace(['stdClass Object', 'Array', '( [', '])', '[', ']', '(', ')'], '', $data);
        $data = str_replace(' =>', ':', $data);
        $data = preg_replace('/\d+:/', '', $data);
        return $data;
    }
}