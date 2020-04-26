<?php

declare(strict_types=1);

namespace App\Api\Upload;

use App\Config\Env;
use App\Api\AbsApi;
use App\Db\UserDbCollection;
use App\Db\ImagesDbCollection;
use App\Helper\ResponseFactory;
use App\Helper\FileValidator;
use App\Img\Blob;
use App\Img\ImgTransform;
use App\Img\ComputerVision;
// use App\Img\UploadException;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Exception\UploadedFileErrorException;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\Exceptions\InvalidArgumentTypeException;

// images will be stored under https://AZURE_BLOB_ACCOUNT.blob.core.windows.net/AZURE_CONTAINER/IMAGE_NAME

class UploadFileApi extends AbsApi {
    protected $folder;
    protected $expiry;

    public function __construct(
        Blob $blob,
        ComputerVision $computerVision,
        FileValidator $validator,
        ImagesDbCollection $imagesDb,
        UserDbCollection $userDb,
        ResponseFactory $responseFactory
    ) {
        // get database connection
        $this->computerVision = $computerVision;
        $this->validator = $validator;
        $this->userDb = $userDb;
        $this->imagesDb = $imagesDb;
        $this->responseFactory = $responseFactory;
        
        // get upload folder 
        try {
			parent::__construct();
			
            $this->folder = Env::get('UPLOAD_FOLDER');
            $this->expiry = 'P'.Env::get('AZURE_BLOB_SAS_EXPIRY_YEARS').'Y';
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
        $filepath = sprintf("$this->folder/%s_%s", uniqid(), $filepath);
        $file->moveTo($filepath);
        return $filepath;
    }

    /**
     * Create thumbnail
     
    protected function createThumbnail(string $filepath) :string {
        $thumbnailName = ImgTransform::getThumbnailName($filepath);
        ImgTransform::thumbnail($filepath, $thumbnailName);

        return $thumbnailName;
    }
    */


    /**
     * Main upload function
     * @access public
     */
    public function execute(ServerRequestInterface $request) :ResponseInterface {
        $data = json_decode($request->getParsedBody()['json']);
        $headers = $this->headers;
        $uploadedFiles = $request->getUploadedFiles();

        foreach ($uploadedFiles AS $file) {
        try {
           // check if user is valid
            $this->getUserId($data->user);

            $filepath = str_replace(' ', '-', $file->getClientFilename());

            // upload locally
            $filepath = $this->uploadLocal($filepath, $file);

            // validate file as image
            $this->validateUpload($filepath);

            // create thumbnail
        //    $thumbnailName = $this->createThumbnail($filepath);

            // extract the exif data..
            $exif = exif_read_data($filepath, "FILE,COMPUTED,ANY_TAG,IFD0,THUMBNAIL,COMMENT,EXIF", true);
            $exif = $exif === false ? '' : json_encode($exif);       
   
            // store img on blob (with its thumbnail)
            $this->storeOnCloud($filepath);
        //    $this->storeOnCloud($thumbnailName);

            // generate sas url with default expiry date
            $sasUrl = $this->blob->generateBlobDownloadLinkWithSAS(
                basename($filepath),
                $this->expiry
            );
/*var_dump(__LINE__, $sasUrl, filter_var($sasUrl, FILTER_VALIDATE_URL));
exit;
  */        // perform computer vision and store given tags..
            $tags = [];
            $cvResponse = $this->computerVision->getAnalysis($sasUrl);
            foreach ($cvResponse['categories'] AS $v) {
                if ($v['score'] > $this->computerVision->getThreshold()) {
                    $tags[] = $v['name'];
                }
            }

            $date = new \DateTime(date('Y-m-d'));
            $date->add(new \DateInterval($this->expiry));
            
             // setup Image object
            $this->imagesDb->mapObj->setFilename(basename($filepath)); 
            $this->imagesDb->mapObj->setUrl($sasUrl); 
            $this->imagesDb->mapObj->setUserId($this->userDb->mapObj->getId());
            $this->imagesDb->mapObj->setTags($tags);
            $this->imagesDb->mapObj->setExif($exif);
            $this->imagesDb->mapObj->setExpiry($date->format('Y-m-d'));

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

        // app errors
        } catch (\InvalidArgumentException $e) {
            return $this->setResponse(400, $e->getMessage(), $headers);

        } catch (\Exception $e) {
            return $this->setResponse(500, $e->getMessage(), $headers);
       
        } finally {
            // if blob exists delete it?
        }
        }
        // 201 created
        return $this->setResponse(201, 'Successful Upload!', $headers);
    }
}