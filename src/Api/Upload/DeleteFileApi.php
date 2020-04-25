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

class DeleteFileApi extends AbsApi {
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

            // cycle through images and prepare queries
            foreach ($data AS $k=>$v) {

                // find the image & verify that this user has privileges to delete it
                $image = $this->imagesDb->select(['filename' => $v])->toArray();

                if (count($image) < 1 || is_null($image[0]->_id)) {
                    throw new \InvalidArgumentException('Cannot find the given image to be deleted.');
                }

                if ($image[0]->userId->__toString() != $this->userDb->mapObj->getId()->__toString()) {
                    throw new \InvalidArgumentException('User does not have enough privilges to perform this action.');
                }
                // then delete from db AND from blob
                $this->imagesDb->setupQuery('delete', ['_id' => $image[0]->_id]);
            }


            // execute queries
            if (! $this->imagesDb->executeQueries()) {
                return $this->setResponse(500, 'Unable to execute the required operation.', $headers);
            }

            // then delete from blob
            foreach ($data AS $k=>$v) {
                $this->blob->delete($v);
            }

        // Azure errors
        }  catch (InvalidArgumentTypeException $e) {
            return $this->setResponse(400, $e->getMessage(), $headers);

        } catch (ServiceException $e) {
            return $this->setResponse(500, $e->getMessage(), $headers);

        // app errors
        } catch (\InvalidArgumentException $e) {
            return $this->setResponse(400, $e->getMessage(), $headers);

        } catch (\Exception $e) {
            return $this->setResponse(500, $e->getMessage(), $headers);
        } 
        // 204 Successfully deleted
        return $this->setResponse(204, 'Images deleted!', $headers);
    }
}