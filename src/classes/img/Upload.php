<?php
declare(strict_types=1);

namespace App\Img;

use App\Img\UploadException;

class Upload {
    const UPLOAD_INPUT_NAME = 'photo';
    const MAX_FILENAME_LEN = 250;

    protected $uploadDir;
    protected $validTypes = ['image/gif;', 'image/jpeg;', 'image/png;'];
    

    public function __construct(string $uploadDir) {
        $this->uploadDir = dirname(__DIR__, 3) . $uploadDir;
    }


    protected function isValidFilename(string $name) : bool {
        if (strlen($name) > self::MAX_FILENAME_LEN)
            return false;
        
        if (mb_detect_encoding($name, 'UTF-8') === false)
            return false;
        
        return true;
    }


    protected function isValidFile(): bool {
        $finfo = new \finfo(FILEINFO_MIME);
        $type = $finfo->file($_FILES[self::UPLOAD_INPUT_NAME]['tmp_name']);
        preg_match("/image\/.*;/", $type, $res);
        if (is_array($res))
            if (count($res) > 0)
                $res = $res[0];

        if (! in_array($res, $this->validTypes) || 
            strpos($type, pathinfo($_FILES[self::UPLOAD_INPUT_NAME]['tmp_name'], PATHINFO_EXTENSION)) === false)
        {
            return false;
        }

        return true;
    }


    public function upload():void {
        if (! isset($_FILES[self::UPLOAD_INPUT_NAME]['error'])) {
            throw new UploadException(UploadException::EMPTY_FILE);
        }

        if ($_FILES[self::UPLOAD_INPUT_NAME]['error'] != UPLOAD_ERR_OK) {
            throw new UploadException($_FILES[self::UPLOAD_INPUT_NAME]['error']);
        }

        if (! $this->isValidFilename($_FILES[self::UPLOAD_INPUT_NAME]['tmp_name']))
            throw new UploadException(UploadException::INVALID_FILENAME);

        if (file_exists($_FILES[self::UPLOAD_INPUT_NAME]['tmp_name'])) {
            if(! $this->isValidFile()) {
                throw new UploadException(UploadException::INVALID_FILE);
            }
        } else {
            throw new UploadException(UploadException::EMPTY_FILE);
        }    

        if (! move_uploaded_file(
            $_FILES[self::UPLOAD_INPUT_NAME]['tmp_name'], 
            $this->uploadDir . $_FILES[self::UPLOAD_INPUT_NAME]['name']
        )) {
            throw new UploadException();
        }
    }

}