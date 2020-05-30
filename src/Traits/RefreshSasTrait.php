<?php
declare(strict_types=1);

namespace App\Traits;

use MongoDB\BSON\ObjectId;
use App\Img\Blob;
use App\Config\Env;
use App\Db\BaseDbCollection;


Trait RefreshSasTrait {
    /**
     * @access protected
     * @var Blob $blob Blob class instance to handle refresh of SAS tokens
     */
    protected $blob;


    /**
     * After all photos are passed to this method 
     * 
     * @param array $data Values returned from a select query
     * @param BaseDbCollection $collection MongoDb collection handeling class (usually will be valued to $this)
     * @param string $sasField Name of the db field that stores the sas url (default is 'url')
     * @param string $filenameField Name of the db field that stores the file name (default is 'filename')
     */
    protected function refreshSasForExpiredItems(
        array $data, 
        BaseDbCollection $collection, 
        string $expiry,
        string $sasField = 'url',
        string $filenameField = 'filename',
        string $expiryField = 'expiry'
    ): void {        
        // cycle all data to look for urls that may expire
        foreach ($data AS $key => $item) {
            $date = new \DateTime($item->{$expiryField});
            $date->add(new \DateInterval($expiry));

            //refresh url + expiry date
            $item->$sasField = $this->blob->generateBlobDownloadLinkWithSAS($item->$filenameField, $expiry);
            $item->$expiryField = $date->format('Y-m-d');

            $mapObjAttributes = array_keys(get_object_vars($item));
            
            // set all values to mapObj inside DbCollection so it will be ready for update
            foreach ($mapObjAttributes AS $attribute) {
                $setter = sprintf("set%s", ucfirst(str_replace('_', '', $attribute)));
                $collection->mapObj->{$setter}($item->{$attribute});
// var_dump($setter, $item->{$attribute});
            }

            // setup update from id
            $collection->setupQuery('update', ['_id' => $collection->mapObj->getId()]);
        }

        if ($collection->executeQueries() === false){
            throw new \Exception("Db query error.");
        }
    }
}