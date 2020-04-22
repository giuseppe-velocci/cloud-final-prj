<?php

declare(strict_types=1);

namespace App\Api\Upload;

use App\Config\Env;
use App\Db\UserDbCollection;
use App\Db\ImagesDbCollection;
use App\Img\Blob;
use App\Helper\ResponseFactory;
use App\Api\AbsApi;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class GetImageApi extends AbsApi {
    public function __construct(
        Blob $blob,
        ImagesDbCollection $imagesDb,
        UserDbCollection $userDb,
        ResponseFactory $responseFactory
    ) {
        $this->blob = $blob;
        $this->imagesDb = $imagesDb;
        $this->userDb = $userDb;
        $this->responseFactory = $responseFactory;

        try {
			$this->cookieParam = Env::get('HTTP_COOKIE_PARAM');
			$this->headers = Env::get('API_HEADERS');
			$this->config  = Env::get('API_CONFIG');
			
        } catch (\InvalidArgumentException $e) {
            die($e->getMessage());
        }
    }

    /**
     * Main upload function
     * @access public
     */
    public function execute(ServerRequestInterface $request) :ResponseInterface {

    }

}