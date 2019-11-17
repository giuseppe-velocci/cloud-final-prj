<?php
declare(strict_types=1);

namespace App\Img;

class UploadException extends \Exception
{
    const EMPTY_FILE = 9;
    const INVALID_FILENAME = 10;
    const INVALID_FILE = 11;

    public function __construct(int $code=0) {
        $message = $this->codeToMessage($code);
        parent::__construct($message, $code);
    }

    private function codeToMessage($code)
    {
        switch ($code) {
            case UPLOAD_ERR_INI_SIZE:
                $message = "The uploaded file exceeds the upload_max_filesize directive in php.ini";
                break;
            case UPLOAD_ERR_FORM_SIZE:
                $message = "The uploaded file exceeds the MAX_FILE_SIZE directive that was specified in the HTML form";
                break;
            case UPLOAD_ERR_PARTIAL:
                $message = "The uploaded file was only partially uploaded";
                break;
            case UPLOAD_ERR_NO_FILE:
                $message = "No file was uploaded";
                break;
            case UPLOAD_ERR_NO_TMP_DIR:
                $message = "Missing a temporary folder";
                break;
            case UPLOAD_ERR_CANT_WRITE:
                $message = "Failed to write file to disk";
                break;
            case UPLOAD_ERR_EXTENSION:
                $message = "File upload stopped by extension";
                break;

            case UploadException::EMPTY_FILE:
                $message = "Empty or non existing file uploaded";
                break;
            case UploadException::INVALID_FILENAME:
                $message = "File name too long or with invalid characters";
                break;    
            case UploadException::INVALID_FILE:
                $message = "Unsupported file type";
                break; 

            default:
                $message = "Unknown upload error";
                break;
        }
        return $message;
    }
}