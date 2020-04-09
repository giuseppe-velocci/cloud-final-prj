<?php

declare(strict_types=1);

namespace App\Api\Upload;


use App\Helper\ResponseFactory;
use App\Api\AbsApi;
use App\Db\Images;
use App\Db\ImagesDbCollection;
use App\Config\Env;
use App\Img\UploadException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;


class UploadFileApi extends AbsApi {
    protected $folder;

    public function __construct(
        Images $images, 
        ImagesDbCollection $imagesDb,
        ResponseFactory $responseFactory
    ) {
        // get database connection
        $this->images = $images;
        $this->imagesDb = $imagesDb;
        $this->responseFactory = $responseFactory;

        chdir(dirname(__DIR__, 3));
		$this->config = require_once 'config/api.php';
        date_default_timezone_set($this->config['timezone']);
        
        // get upload folder 
        try {
            $this->folder = Env::get('UPLOAD_FOLDER');

        } catch (\InvalidArgumentException $e) {
            die($e->getMessage());
        }
    }

    public function execute(ServerRequestInterface $request) :ResponseInterface {
        $headers = $this->config['headers'];
        $uploadedFiles = $request->getUploadedFiles();
/*
var_dump($uploadedFiles);
exit;
*/
        foreach ($uploadedFiles AS $file) {
            if (UPLOAD_ERR_OK !== $file->getError()) {
                return $this->setResponse(
                    400, 
                    sprintf(
                        'Upload error: %s',
                        \Zend\Diactoros\UploadedFile::ERROR_MESSAGES[$file->getError()]
                    ), 
                    $headers
                );
            }

            try {
              //  echo sprintf("$this->folder/%s", $file->getClientFilename());
                $file->moveTo( //$this->folder);
                sprintf("$this->folder/%s", $file->getClientFilename()));

            } catch (\Zend\Diactoros\Exception\UploadedFileErrorException $e) {
                return $this->setResponse(400, $e->getMessage(), $headers);
            }
        }

        return $this->setResponse(200, 'Successful Upload!', $headers);
    }
}