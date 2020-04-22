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
    protected function refreshSas(
        array $data, 
        BaseDbCollection $collection, 
        string $sasField = 'url',
        string $filenameField = 'filename'
    ): array {
        try {
            $expiry = Env::get('AZURE_BLOB_SAS_EXPIRY');            
        } catch (\InvalidArgumentException $e) {
            die($e->getMessage());
        }

        $tomorrowDate = new \DateTime(date("Y-m-d"));
        $tomorrowDate->add(new \DateInterval('P1D'));
        $tomorrow = $tomorrowDate->format("Y-m-d");

        // cycle all data to look for urls that may expire
        foreach ($data AS $key => $item) {
            // if current value is not to expire tomorrow: continue
            if (strpos($item->$sasField, "se=$tomorrow") === false) {
                continue;
            }
/**/
            // if a value that will expire tomorrow is found -> refresh url
            $item->$sasField = $this->blob->generateBlobDownloadLinkWithSAS($item->$filenameField, $expiry);
            $mapObjAttributes = array_keys(get_object_vars($item));
            
            // set all values to mapObj inside DbCollection so it will be ready for update
            foreach ($mapObjAttributes AS $attribute) {
                $setter = sprintf("set%s", ucfirst(str_replace('_', '', $attribute)));
                $collection->mapObj->{$setter}($item->{$attribute});
                var_dump($setter, $item->{$attribute});
            }

            // setup update from id
            $collection->setupQuery('update', ['_id' => $collection->mapObj->getId()]);
            $data[$key]->$sasField = $item->$sasField; // replace the old sas with the new on in data
        }

        try {
            if ($collection->executeQueries() === false){
                // .. throw error
            }
        } catch (\InvalidArgumentexception $e) {
            //.. do nothing since no queries were added
        }
        
        return $data;
    }
}