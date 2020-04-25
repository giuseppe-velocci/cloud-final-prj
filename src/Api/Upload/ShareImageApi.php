<?php

declare(strict_types=1);

namespace App\Api\Upload;

use App\Config\Env;
use App\Db\UserDbCollection;
use App\Db\ImagesDbCollection;
use App\Db\ShareDbCollection;
use App\Helper\ResponseFactory;
use App\Helper\Guid;
use App\Helper\Mailer;
use App\Api\AbsApi;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

class ShareImageApi extends AbsApi {
    public function __construct(
        ImagesDbCollection $imagesDb,
        UserDbCollection $userDb,
        ShareDbCollection $shareDb,
        Guid $guid,
        Mailer $mailer,
        ResponseFactory $responseFactory
    ) {
        $this->imagesDb = $imagesDb;
        $this->userDb   = $userDb;
        $this->shareDb  = $shareDb;
        $this->guid = $guid;
        $this->mailer = $mailer;
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
            if (count($data) == 1) {
                throw new \InvalidArgumentException('At least an image must be selected for deletion.');
            }    

            // find current user (to validate he is the owner of the files)
            if (! $this->userDb->findByEmail($data['user'])) {
                throw new \InvalidArgumentException('Wrong data provided.');
            }

            unset($data['user']);
            
            // find the image & verify that this user has privileges to delete it
            $image = $this->imagesDb->select(['filename' => $v])->toArray();

            if (count($image) < 1 || is_null($image[0]->_id)) {
                throw new \InvalidArgumentException('Cannot find the given image to be deleted.');
            }

            if ($image[0]->userId->__toString() != $this->userDb->mapObj->getId()->__toString()) {
                throw new \InvalidArgumentException('User does not have enough privilges to perform this action.');
            }
            // then delete from db AND from blob
            $guid = $this->guid->generate();
            
            $this->shareDb->mapObj->setImgUrl($imgUrl);
            $this->shareDb->mapObj->setUrlGuid($guid);
            $this->shareDb->mapObj->setEmail($data['email']);
            $this->shareDb->mapObj->setExpiry($data['expiry']);

            $this->shareDb->setupQuery('insert');
            
            // execute queries
            if (! $this->imagesDb->executeQueries()) {
                return $this->setResponse(500, 'Unable to execute the required operation.', $headers);
            }

            // send email
            $this->mailer->mail(
                $data['email'], 
                sprintf('$s has shared a photo with you!', $data['user']),
                sprintf(
                    'You have a new photo to see.'
                    . ' Follow this link<a href="%s">%s</a>.'
                    . ' Hurry up: it will expire by %s.<br/>Cloudpj images.',
                    ''
                )
            );

        // app errors
        } catch (\InvalidArgumentException $e) {
            $response = $this->setResponse(400, $e->getMessage(), $headers);

        } catch (\Exception $e) {
            $response = $this->setResponse(500, $e->getMessage(), $headers);
        }

        if (is_null($response)) {
            $response = $this->setResponse(200, 'Image Shared!', $headers);
        }

        return $response->withQueryParams(['filename' => $data['filename']]);
    }
}