<?php
declare(strict_types=1);

namespace App\Img;

use App\Img\UploadException;
use App\Img\ImgValidator;

class Upload {
    const UPLOAD_INPUT_NAME = 'photo';

    protected $uploadDir;
    protected $validTypes = ['image/gif;', 'image/jpeg;', 'image/png;'];
    protected $imgValidator;

    /**
     * @$uploadDir string = folder name starting from project folder (1 level above public folder)
     */
    public function __construct(string $uploadDir) {
        $this->uploadDir = dirname(__DIR__, 3) . $uploadDir;
        $imgValidator = new ImgValidator($this->validTypes);
    }

    
    /**
     * @return string $uploadedFilename = full path of uploaded file
     */
    public function upload():string {
        if (! isset($_FILES[self::UPLOAD_INPUT_NAME]['error'])) {
            throw new UploadException(UploadException::EMPTY_FILE);
        }

        if ($_FILES[self::UPLOAD_INPUT_NAME]['error'] != UPLOAD_ERR_OK) {
            throw new UploadException($_FILES[self::UPLOAD_INPUT_NAME]['error']);
        }

        if (! $this->imgValidator->isValidFilename($_FILES[self::UPLOAD_INPUT_NAME]['tmp_name']))
            throw new UploadException(UploadException::INVALID_FILENAME);

        if (file_exists($_FILES[self::UPLOAD_INPUT_NAME]['tmp_name'])) {
            if(! $this->imgValidator->isValidFile($_FILES[self::UPLOAD_INPUT_NAME]['tmp_name'])) {
                throw new UploadException(UploadException::INVALID_FILE);
            }
        } else {
            throw new UploadException(UploadException::EMPTY_FILE);
        }    

        $uploadadFilename = $this->uploadDir . 
            basename(filter_var($_FILES[self::UPLOAD_INPUT_NAME]['name'], FILTER_SANITIZE_URL));
        if (! move_uploaded_file(
            $_FILES[self::UPLOAD_INPUT_NAME]['tmp_name'], 
            $uploadadFilename
        )) {
            throw new UploadException();
        }

        return $uploadadFilename;
    }
}