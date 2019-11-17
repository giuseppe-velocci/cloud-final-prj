<?php
declare(strict_types=1);

namespace App\Img;

class ImgValidator {
    const MAX_FILENAME_LEN = 128;
    protected $validTypes;


    public function __construct(array $validTypes) {
        $this->validTypes = $validTypes;
    }

    /**
     * @return bool = wheather filename is valid (utf-8 + maxlen)
     */
    public function isValidFilename(string $name) : bool {
        if (strlen($name) < 1)
            return false;

        if (strlen(basename($name)) > self::MAX_FILENAME_LEN)
            return false;

        if (! ctype_alnum(basename($name)[0]))
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

        preg_match('/image\/\w+/', trim($type), $res);

        if (is_array($res))
            if (count($res) > 0)
                $res = $res[0];

        $fileExtension = pathinfo($filepath, PATHINFO_EXTENSION);
        $fileExtension = $fileExtension == 'jpg' ? 'jpeg' : $fileExtension;

        if (! in_array($res, $this->validTypes) 
            || strpos($res, $fileExtension) === false)
        {
            return false;
        }

        return true;
    }
}