<?php

declare(strict_types=1);

namespace App\Api\Upload;

use App\Config\Env;
use App\Db\UserDbCollection;
use App\Db\ImagesDbCollection;
use App\Img\Blob;
use App\Helper\ResponseFactory;
use App\Api\AbsApi;

class UploadFileApi extends AbsApi {
    public function __construct(
        Blob $blob,
        ImagesDbCollection $imagesDb,
        UserDbCollection $userDb,
        ResponseFactory $responseFactory
    ) {
        $blob = $this->blob;
        $imagesDb = $this->imagesDb;
        $userDb = $this->userDb;
        $responseFactory = $this->responseFactory;

        chdir(dirname(__DIR__, 3));
		$this->config = require_once 'config/api.php';
        date_default_timezone_set($this->config['timezone']);
    }

    /**
     * Main upload function
     * @access public
     */
    public function execute(ServerRequestInterface $request) :ResponseInterface {
        $data = json_decode($request->getParsedBody()['json']);
        $headers = $this->config['headers'];
        $uploadedFiles = $request->getUploadedFiles();



    }
}