<?php

declare(strict_types=1);

namespace App\Api\Upload;

use App\Config\Env;
use App\Api\AbsApi;
use App\Db\UserDbCollection;
use App\Db\ImagesDbCollection;
use App\Img\Blob;
use App\Helper\ResponseFactory;
use App\Helper\FileValidator;
use App\Img\ImgTransform;
// use App\Img\UploadException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Exception\UploadedFileErrorException;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\Exceptions\InvalidArgumentTypeException;

// images will be stored under https://AZURE_BLOB_ACCOUNT.blob.core.windows.net/AZURE_CONTAINER/IMAGE_NAME

class UploadFileApi extends AbsApi {
    protected $folder;

    public function __construct(
        Blob $blob,
        FileValidator $validator,
        ImagesDbCollection $imagesDb,
        UserDbCollection $userDb,
        ResponseFactory $responseFactory
    ) {
        // get database connection
        $this->validator = $validator;
        $this->userDb = $userDb;
        $this->imagesDb = $imagesDb;
        $this->responseFactory = $responseFactory;

        chdir(dirname(__DIR__, 3));
		$this->config = require_once 'config/api.php';
        date_default_timezone_set($this->config['timezone']);
        
        // get upload folder 
        try {
            $this->folder = Env::get('UPLOAD_FOLDER');
            $this->blob = $blob;

        } catch (\InvalidArgumentException $e) {
            die($e->getMessage());
        }
    }

    /**
     * store on Azure Cloud
     */
    protected function storeOnCloud(string $filepath) :void {
        $this->blob->upload($filepath);
    }

    /**
     * Get user id
     */
    protected function getUserId ($user): void {
        if (! $this->userDb->findByEmail($user)) {
            throw new \InvalidArgumentException('Wrong data provided.');
        }
    }

    /**
     * Validate uploaded file
     */
    protected function validateUpload(string $filepath) :void {
        $this->validator->validateFilename($filepath);
        $this->validator->validateFile($filepath);
    }

    /**
     * Upload locally
     */
    protected function uploadLocal(string $filepath, $file) :string {
        if (UPLOAD_ERR_OK !== $file->getError()) {
            throw new \InvalidArgumentException(
                sprintf('Upload error: %s',
                    \Zend\Diactoros\UploadedFile::ERROR_MESSAGES[$file->getError()]
                )
            );
        }
        $filepath = sprintf("$this->folder/%s", $filepath);
        $file->moveTo($filepath);
        return $filepath;
    }

    /**
     * Create thumbnail
     */
    protected function createThumbnail(string $filepath) :string {
        $thumbnailName = ImgTransform::getThumbnailName($filepath);
        ImgTransform::thumbnail($filepath, $thumbnailName);

        return $thumbnailName;
    }


    /**
     * Main upload function
     * @access public
     */
    public function execute(ServerRequestInterface $request) :ResponseInterface {
        $data = json_decode($request->getParsedBody()['json']);
        $headers = $this->config['headers'];
        $uploadedFiles = $request->getUploadedFiles();

        foreach ($uploadedFiles AS $file) {
        try {
            $filepath = $file->getClientFilename();

            // upload locally
            $filepath = $this->uploadLocal($filepath, $file);

            // validate file as image
            $this->validateUpload($filepath);

            // create thumbnail
        //    $thumbnailName = $this->createThumbnail($filepath);

            // extract the exif data..
            

            // store img on blob (with its thumbnail)
            $this->storeOnCloud($filepath);
        //    $this->storeOnCloud($thumbnailName);
            
            // perform computer vision and store given tags..


            // setup Image object
            $this->getUserId($data->user);
            // which url? Maybe from blob
            $this->imagesDb->mapObj->setUrl($file->getClientFilename()); 
            $this->imagesDb->mapObj->setUserId($this->userDb->mapObj->getId());
            $this->imagesDb->mapObj->setTags([]); // tags?
            $this->imagesDb->mapObj->setExif([]); // exif?

            // now store on db
            $this->imagesDb->setupQuery('insert');
            if (! $this->imagesDb->executeQueries()) {
                return $this->setResponse(500, 'Unable to store images information.', $headers);
            }
        // local upload errors
        } catch (UploadedFileErrorException $e) {
            return $this->setResponse(400, $e->getMessage(), $headers);

        // Azure errors
        }  catch (InvalidArgumentTypeException $e) {
            return $this->setResponse(400, $e->getMessage(), $headers);

        } catch (ServiceException $e) {
            return $this->setResponse(500, $e->getMessage(), $headers);

        } catch (\InvalidArgumentException $e) {
            return $this->setResponse(400, $e->getMessage(), $headers);

        } catch (\Exception $e) {
            return $this->setResponse(500, $e->getMessage(), $headers);
        
        // in the end ALWAYS remove the uploaded file(s)
        } finally {
            if (file_exists($filepath)) {
                unlink($filepath);
            }
          /*  if (isset($thumbnailName) && file_exists($thumbnailName)) {
                unlink($thumbnailName);
            }
            */
        }
        }
        return $this->setResponse(200, 'Successful Upload!', $headers);
    }
}