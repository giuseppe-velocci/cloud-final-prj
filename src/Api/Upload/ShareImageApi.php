<?php

declare(strict_types=1);

namespace App\Api\Upload;

use App\Config\Env;
use App\Img\Blob;
use App\Db\UserDbCollection;
use App\Db\ImagesDbCollection;
use App\Helper\ResponseFactory;
use App\Helper\Guid;
use App\Api\AbsApi;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ShareImageApi extends AbsApi {
    public function __construct(
        ImagesDbCollection $imagesDb,
        UserDbCollection $userDb,
        Guid $guid,
        Blob $blob,
        ResponseFactory $responseFactory
    ) {
        $this->imagesDb = $imagesDb;
        $this->userDb   = $userDb;
        $this->guid = $guid;
        $this->blob = $blob;
        $this->responseFactory = $responseFactory;

        try {
			parent::__construct();
			
        } catch (\InvalidArgumentException $e) {
            die($e->getMessage());
        }

        date_default_timezone_set($this->config['timezone']);
    }

    /**
     * Main upload function
     * @access public
     */
    public function execute(ServerRequestInterface $request) :ResponseInterface {
        $data = json_decode($request->getParsedBody()['json'], true);
        $headers = $this->headers;
        $response = null;

        // try block to delete images
        try {
           
            // find current user (to validate he is the owner of the files)
            if (! $this->userDb->findByEmail($data['user'])) {
                throw new \InvalidArgumentException('Wrong data provided.');
            }

            unset($data['user']);

            // block if date less than today
            if (strtotime($data['expiry']) - time() <= 0) {
                throw new \InvalidArgumentException('The given date must be later than today.');
            }

            // find the image & verify that this user has privileges to delete it
            $image = $this->imagesDb->select(['filename' => $data['filename']])->toArray();

            if (count($image) < 1 || is_null($image[0]->_id)) {
                throw new \InvalidArgumentException('Cannot find the given image to be deleted.');
            }

            if ($image[0]->userId->__toString() != $this->userDb->mapObj->getId()->__toString()) {
                throw new \InvalidArgumentException('User does not have enough privilges to perform this action.');
            }

            $imageShares =  is_array($image[0]->shares) ?  $image[0]->shares : get_object_vars($image[0]->shares);
            // check if already exists a link with the same given date
            $isFound = array_filter($imageShares, function($share) use($data) {
                return preg_match('/'.$data['expiry'].'/', $share);
            });

            if (count($isFound) > 0) {
                    throw new \InvalidArgumentException('Another shareable link with the same date exists.');
                }

            $imageShares[$this->guid->generate()] = 
                $this->blob->generateBlobDownloadLinkWithSAS($image[0]->filename, $data['expiry'])
            ;

            $this->imagesDb->mapObj->setId($image[0]->_id);
            $this->imagesDb->mapObj->setFilename($image[0]->filename); 
            $this->imagesDb->mapObj->setUrl($image[0]->url); 
            $this->imagesDb->mapObj->setUserId($image[0]->userId);
            $this->imagesDb->mapObj->setTags($image[0]->tags);
            $this->imagesDb->mapObj->setExif(json_decode(json_encode($image[0]->exif), true));
            $this->imagesDb->mapObj->setShares($imageShares);

            $this->imagesDb->setupQuery('update', ['_id' => $image[0]->_id]);
            
            // execute queries
            if (! $this->imagesDb->executeQueries()) {
                return $this->setResponse(500, 'Unable to execute the required operation.', $headers);
            }


        // app errors
        } catch (\InvalidArgumentException $e) {
            $response = $this->setResponse(400, $e->getMessage(), $headers);

        } catch (\Exception $e) {
            $response = $this->setResponse(500, $e->getMessage(), $headers);
        }

        if (is_null($response)) {
            $response = $this->setResponse(201, 'Image Shared!', $headers);
        }

        return $response->withAddedHeader('Referer', $data['filename']);
    }
}