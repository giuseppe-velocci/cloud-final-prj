<?php
declare(strict_types=1);

namespace App\Img;

class ImgValidator {
    const MAX_FILENAME_LEN = 250;
    protected $validTypes;


    public function __construct(array $validTypes) {
        $this->validTypes = $validTypes;
    }

    /**
     * @return bool = wheather filename is valid (utf-8 + maxlen)
     */
    public function isValidFilename(string $name) : bool {
        if (strlen($name) > self::MAX_FILENAME_LEN)
            return false;
        
        if (mb_detect_encoding($name, 'UTF-8') === false)
            return false;
        
        return true;
    }


    /**
     * @return bool = wheather file type is included in valid type array
     */
    public function isValidFile(string $filepath): bool {
        $finfo = new \finfo(FILEINFO_MIME);
        $type = $finfo->file($filepath);

        preg_match("/image\/.*;/", $type, $res);

        if (is_array($res))
            if (count($res) > 0)
                $res = $res[0];

        if (! in_array($res, $this->validTypes) 
            || strpos($type, pathinfo($filepath, PATHINFO_EXTENSION)) === false)
        {
            return false;
        }

        return true;
    }
}