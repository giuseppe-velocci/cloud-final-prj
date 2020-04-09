<?php
declare(strict_types=1);

namespace App\Img;

use App\Img\UploadException;
use App\Img\ImgValidator;

class Upload {
    const UPLOAD_INPUT_NAME = 'photo';

    protected $uploadDir;
    protected $validTypes = ['image/jpeg', 'image/png', 'image/gif'];
 //   protected $imgValidator;

    /**
     * @$uploadDir string = folder name starting from project folder (1 level above public folder)
     */
    public function __construct(string $uploadDir) {
        $this->uploadDir = dirname(__DIR__, 3) . $uploadDir;
    }


    /**
     * @?string $key = key for $_FILES array
     * @return string $uploadedFilename = full path of uploaded file
     */
    public function uploadImg(?string $key=null):string {
        $key = is_null($key) ? $key = self::UPLOAD_INPUT_NAME : $key;
        $imgValidator = new ImgValidator($this->validTypes);

        if (! isset($_FILES[$key]['error'])) {
            throw new UploadException(UploadException::EMPTY_FILE);
        }

        if ($_FILES[$key]['error'] != UPLOAD_ERR_OK) {
            throw new UploadException($_FILES[$key]['error']);
        }

        if (! $imgValidator->isValidFilename($_FILES[$key]['tmp_name']))
            throw new UploadException(UploadException::INVALID_FILENAME);
        
        $uploadadFilename = 
            basename(filter_var($_FILES[$key]['name'], FILTER_SANITIZE_URL));
        if (! $imgValidator->isValidFilename($uploadadFilename))
            throw new UploadException(UploadException::INVALID_FILENAME);

        if (file_exists($_FILES[$key]['tmp_name'])) {
            if(! $imgValidator->isValidFile($_FILES[$key]['tmp_name'])) {
                throw new UploadException(UploadException::INVALID_FILE);
            }
        } else {
            throw new UploadException(UploadException::EMPTY_FILE);
        }    

        
        if (! move_uploaded_file(
            $_FILES[$key]['tmp_name'], 
            $this->uploadDir . $uploadadFilename
        )) {
            throw new UploadException();
        }

        return $uploadadFilename;
    }
}