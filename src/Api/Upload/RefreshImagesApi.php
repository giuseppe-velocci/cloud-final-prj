<?php

declare(strict_types=1);

namespace App\Api\Upload;

use App\Config\Env;
use App\Api\AbsApi;
use App\Db\UserDbCollection;
use App\Db\ImagesDbCollection;
use App\Helper\ResponseFactory;
use App\Img\Blob;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

// images will be stored under https://AZURE_BLOB_ACCOUNT.blob.core.windows.net/AZURE_CONTAINER/IMAGE_NAME

class RefreshImagesApi extends AbsApi {
    use \App\Traits\RefreshSasTrait;

    protected $expiry;
    protected $cookieExpiry;

    public function __construct(
        Blob $blob,
        ImagesDbCollection $imagesDb,
        UserDbCollection $userDb,
        ResponseFactory $responseFactory
    ) {
        // get database connection
        $this->userDb = $userDb;
        $this->imagesDb = $imagesDb;
        $this->responseFactory = $responseFactory;
        
        // get upload folder 
        try {
			parent::__construct();
			
            $this->expiry = 'P'.Env::get('AZURE_BLOB_SAS_EXPIRY_YEARS').'Y';
            $this->cookieExpiry = 'P'.Env::get('COOKIE_EXPIRY_IN_DAYS').'D';
            $this->blob = $blob;

        } catch (\InvalidArgumentException $e) {
            die($e->getMessage());
        }
    }


    public function execute(ServerRequestInterface $request) :ResponseInterface {
        $data = json_decode($request->getParsedBody()['json'], true);
        $headers = $this->headers;

        // try block to delete images
        try {
            // find current user (to validate he is the owner of the files)
            if (! $this->userDb->findByEmail($data['email'])) {
                return $this->setResponse(400, 'Wrong data provided.', $headers);
            }

            $date = new \DateTime(date("Y-m-d"));
            $date->add(new \DateInterval($this->cookieExpiry));

            $search = ['expiry' => ['$lt' => $date->format('Y-m-d')]];

            $imagesToRefresh = $this->imagesDb->selectAllByUser(
                $this->userDb->mapObj->getId(),
                $search
            );
            if (count($imagesToRefresh))
                $this->refreshSasForExpiredItems($imagesToRefresh, $this->imagesDb, $this->expiry);

        // app errors
        } catch (\Exception $e) {
            return $this->setResponse(500, $e->getMessage(), $headers);
        } 
        // 200 OK
        return $this->setResponse(200, 'Updated shared access signatures!', $headers);
    }
}