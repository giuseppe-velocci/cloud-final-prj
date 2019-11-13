<?php
declare(strict_types=1);

namespace App\Img;

use App\Config\Env;
use MicrosoftAzure\Storage\Blob\BlobRestProxy;
use MicrosoftAzure\Storage\Common\Exceptions\ServiceException;
use MicrosoftAzure\Storage\Common\Exceptions\InvalidArgumentTypeException;

class BlobUpload {
    protected $connectionString;
    protected $containerName;

    // 
    public function __construct() {
        $this->connectionString = "DefaultEndpointsProtocol=https;AccountName=" .
            Env::get('AZURE_BLOB_USER') .
            ";AccountKey=".
            Env::get('AZURE_BLOB_KEY');
        
        $this->containerName = Env::get('AZURE_CONTAINER');
    }



    public function createContainer() {
        // Create blob client.
        $blobClient = BlobRestProxy::createBlobService($this->connectionString);
        
        /*
        # Create the BlobService that represents the Blob service for the storage account
        $createContainerOptions = new CreateContainerOptions();
        
        $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
        
        // Set container metadata.
        $createContainerOptions->addMetaData("key1", "value1");
        $createContainerOptions->addMetaData("key2", "value2");

        $this->containerName = "blockblobs".generateRandomString();

        try {
            // Create container.
            $blobClient->createContainer($this->containerName, $createContainerOptions);
        }*/
    }


    public function upload(string $fileToUpload) : void {
        $blobClient = BlobRestProxy::createBlobService($this->connectionString);

        if (! file_exists($fileToUpload))
            throw new InvalidArgumentTypeException("File not found");

        # Upload file as a block blob
        $content = fopen($fileToUpload, "r");

        //Upload blob
        try {
            $blobClient->createBlockBlob($this->containerName, $fileToUpload, $content);
        } catch(ServiceException $e) {
            throw $e;
        }
    }


    /*
    <form method="post" action="phpQS.php?Cleanup&containerName=<?php echo $containerName; ?>">
        <button type="submit">Press to clean up all resources created by this sample</button>
    </form>
    */
    public function uploadTest(string $fileToUpload): void {
        // Create blob client.
        $blobClient = BlobRestProxy::createBlobService($this->connectionString);

        //$fileToUpload = "HelloWorld.txt"; //

    //    if (!isset($_GET["Cleanup"])) {
            // Create container options object.
            $createContainerOptions = new CreateContainerOptions();
            // Set public access policy. Possible values are
            // PublicAccessType::CONTAINER_AND_BLOBS and PublicAccessType::BLOBS_ONLY.
            // CONTAINER_AND_BLOBS:
            // Specifies full public read access for container and blob data.
            // proxys can enumerate blobs within the container via anonymous
            // request, but cannot enumerate containers within the storage account.
            //
            // BLOBS_ONLY:
            // Specifies public read access for blobs. Blob data within this
            // container can be read via anonymous request, but container data is not
            // available. proxys cannot enumerate blobs within the container via
            // anonymous request.
            // If this value is not specified in the request, container data is
            // private to the account owner.
            $createContainerOptions->setPublicAccess(PublicAccessType::CONTAINER_AND_BLOBS);
            // Set container metadata.
            $createContainerOptions->addMetaData("key1", "value1"); //???
            $createContainerOptions->addMetaData("key2", "value2");
          //  $containerName = "blockblobs".generateRandomString();
            try {
                // Create container.
                $blobClient->createContainer($this->containerName, $createContainerOptions);
                // Getting local file so that we can upload it to Azure
                $myfile = fopen($fileToUpload, "w") or die("Unable to open file!");
                fclose($myfile);
                
                # Upload file as a block blob
                $content = fopen($fileToUpload, "r");
                //Upload blob
                $blobClient->createBlockBlob($this->containerName, $fileToUpload, $content);
                
                // List blobs. (files inside)
                $listBlobsOptions = new ListBlobsOptions();
                $listBlobsOptions->setPrefix("HelloWorld");
                do{
                    $result = $blobClient->listBlobs($this->containerName, $listBlobsOptions);
                    foreach ($result->getBlobs() as $blob)
                    {
                        echo $blob->getName().": ".$blob->getUrl()."<br />";
                    }
                
                    $listBlobsOptions->setContinuationToken($result->getContinuationToken());
                } while($result->getContinuationToken());
                
                // Get blob.
                $blob = $blobClient->getBlob($this->containerName, $fileToUpload);
                // fpassthru($blob->getContentStream());
            }
            catch(ServiceException $e){
                // Handle exception based on error codes and messages.
                // Error codes and messages are here:
                // http://msdn.microsoft.com/library/azure/dd179439.aspx
                $code = $e->getCode();
                $error_message = $e->getMessage();
            }
            catch(InvalidArgumentTypeException $e){
                // Handle exception based on error codes and messages.
                // Error codes and messages are here:
                // http://msdn.microsoft.com/library/azure/dd179439.aspx
                $code = $e->getCode();
                $error_message = $e->getMessage();
            }
    /*    } 
        else 
        {
            try{
                // Delete container.
                echo "Deleting Container".PHP_EOL;
                echo $_GET["containerName"].PHP_EOL;
                echo "<br />";
                $blobClient->deleteContainer($_GET["containerName"]);
            }
            catch(ServiceException $e){
                // Handle exception based on error codes and messages.
                // Error codes and messages are here:
                // http://msdn.microsoft.com/library/azure/dd179439.aspx
                $code = $e->getCode();
                $error_message = $e->getMessage();
                echo $code.": ".$error_message."<br />";
            }
        */
    }
}