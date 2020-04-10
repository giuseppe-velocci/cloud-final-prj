<?php
declare(strict_types=1);

namespace App\Helper;

class FileValidator {
    const MAX_FILENAME_LEN = 128;
    protected $validTypes;


    public function __construct(array $validTypes=[]) {
        $this->validTypes = (! empty($validTypes))? $validTypes : ['image/jpeg', 'image/png', 'image/gif'];
    }

    /**
     * 
     */
    public function validateFilename(string $name) : void {
        if (strlen($name) < 1)
            throw new \InvalidArgumentException("No given name for file.");

        if (strlen(basename($name)) > self::MAX_FILENAME_LEN)
            throw new \InvalidArgumentException("Name too long for file.");

        if (! ctype_alnum(basename($name)[0]))
            throw new \InvalidArgumentException("Invalid characters in file name.");

        if (mb_detect_encoding($name, 'UTF-8') === false)
            throw new \InvalidArgumentException("Invalid characters encoding in file name.");
    }


    /**
     * 
     */
    public function validateFile(string $filepath): void {
        $this->validateFilename($filepath);
        $finfo = new \finfo(FILEINFO_MIME);
        $type = $finfo->file($filepath);
        if (! is_string($type)) {
            throw new \InvalidArgumentException("Invalid file.");
        }

        preg_match('/image\/\w+/', trim($type), $res);

        if (is_array($res))
            if (count($res) > 0)
                $res = $res[0];

        $fileExtension = pathinfo($filepath, PATHINFO_EXTENSION);
        $fileExtension = $fileExtension == 'jpg' ? 'jpeg' : $fileExtension;

        if (! in_array($res, $this->validTypes) 
            || strpos($res, $fileExtension) === false) {
            throw new \InvalidArgumentException("Invalid file.");
        }
    }
}