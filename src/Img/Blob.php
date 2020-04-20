<?php
declare(strict_types=1);

namespace App\Img;

use App\Config\Env;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\Exceptions\InvalidArgumentTypeException;
// to delete files
use MicrosoftAzure\Storage\Blob\BlobSharedAccessSignatureHelper;
use MicrosoftAzure\Storage\Common\Internal\StorageServiceSettings;
use MicrosoftAzure\Storage\Common\Internal\Resources;



// https://stackoverflow.com/questions/60715046/how-to-retrieve-images-from-azure-blob-storage-and-display-on-website-with-php
class Blob {
    protected $connectionString;
    protected $container;

    // 
    public function __construct(string $container='') {
        try {
            $this->connectionString = "DefaultEndpointsProtocol=https;AccountName="
            . Env::get('AZURE_BLOB_ACCOUNT')
            . ";AccountKey="
            . Env::get('AZURE_BLOB_KEY');

            $this->container = strlen($container) > 0 ? $container : Env::get('AZURE_CONTAINER');
        
            
        } catch (\InvalidArgumentException $e) {
            die($e->getMessage());
        }
    }


    /**
     * @access public
     * upload file on the given azure blob container
     */
    public function upload(string $fileToUpload) : void {
        $blobClient = BlobRestProxy::createBlobService($this->connectionString);

        if (! file_exists($fileToUpload)) {
            throw new InvalidArgumentTypeException("File not found");
        }
            
        # Upload file as a block blob
        $content = fopen($fileToUpload, "r");

        //Upload blob
        try {
            $blobClient->createBlockBlob($this->container, basename($fileToUpload), $content);
        
        } catch(ServiceException $e) {
            throw $e;
        } finally {
            fclose($content);
            if (file_exists($fileToUpload))
                unlink($fileToUpload);
        }
    }


    /**
     * delete file(s)
     */
    public function delete(string $fileToDelete) :void {
        $blobClient = BlobRestProxy::createBlobService($this->connectionString);
        try {
            $blobClient->deleteBlob($this->container, $fileToDelete);

        } catch(ServiceException $e) {
            throw $e;
        }  
    }


    /**
     * @param string $expiry Interval in php date->add() format --> "P" + int + "Y|M|D"
     * Alternatively $expiry may take a date in "Y-m-d" format that will be used as exipry date
     */
    public function sasValidityInterval(string $expiry) {
        if (strpos($expiry, 'P') === 0) {
            $date = new \DateTime(date("Y-m-d"));
            $date->add(new \DateInterval($expiry));

        } else {
            // .. should validate date format
            $date = new \DateTime($expiry);
        }
        
        return sprintf('%sT%sZ', $date->format('Y-m-d'), $date->format('H:i:s'));
    }


    /**
     * Generate a temporary SAS share key (also needed with computer vision)
     * std interval: 1 day
     */
    public function generateBlobDownloadLinkWithSAS(string $filename, ?string $interval=null) {
        $settings = StorageServiceSettings::createFromConnectionString($this->connectionString);
        $accountName = $settings->getName();
        $accountKey  = $settings->getKey();

        $helper = new BlobSharedAccessSignatureHelper($accountName, $accountKey);

        $interval   = $interval ?? 'P1D'; // 1 day validity
        $expiry     = $this->sasValidityInterval($interval);

        // Refer to following link for full candidate values to construct a service level SAS
        // https://docs.microsoft.com/en-us/rest/api/storageservices/constructing-a-service-sas
        $sas = $helper->generateBlobServiceSharedAccessSignatureToken(
            Resources::RESOURCE_TYPE_BLOB,
            sprintf('%s/%s', $this->container, $filename),
            'r',      // Read
            $expiry  // A valid ISO 8601 format expiry time                        
            // '2019-01-01T08:30:00Z'//,     
            //'2016-01-01T08:30:00Z',       // A valid ISO 8601 format expiry time
            //'0.0.0.0-255.255.255.255'
            //'https,http'
        );

        $connectionStringWithSAS = Resources::BLOB_ENDPOINT_NAME .
            '='.
            'https://' .
            $accountName .
            '.' .
            Resources::BLOB_BASE_DNS_NAME .
            ';' .
            Resources::SAS_TOKEN_NAME .
            '=' .
            $sas;

        $blobClientWithSAS = BlobRestProxy::createBlobService(
            $connectionStringWithSAS
        );

        // return $blobClientWithSAS;

        // We can download the blob with PHP Client Library
        // downloadBlobSample($blobClientWithSAS);

        // Or generate a temporary readonly download URL link
       
        $blobUrlWithSAS = sprintf(
            '%s%s?%s',
            (string)$blobClientWithSAS->getPsrPrimaryUri(),
            sprintf('%s/%s', $this->container, $filename),
            $sas
        );

        return $blobUrlWithSAS;
/* 
        file_put_contents("outputBySAS.txt", fopen($blobUrlWithSAS, 'r'));

        return $blobUrlWithSAS;
        */
    }




}