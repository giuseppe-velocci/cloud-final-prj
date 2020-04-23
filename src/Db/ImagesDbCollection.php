<?php
declare(strict_types=1);

namespace App\Db;

use MongoDB\BSON\ObjectId;
use App\Db\MongoWQuery;
use App\Helper\ISanitizer;
use App\Config\Env;
use App\Img\Blob;

class ImagesDbCollection extends BaseDbCollection{

    protected $collection = 'images';
    
    public function __construct(
        Images $mapObj,
        MongoWQuery $wQuery,
        ISanitizer $sanitizer,
        Blob $blob
	) {
        parent::__construct(
            $mapObj, $wQuery, $sanitizer
        );

        $this->blob = $blob;
    }
    
    /**
     * @access public
     * Select all images related to current user
     * @param MongoDB\BSON\ObjectId $userId MongoDb Id for the user
     * @param bool $refresh Tells if resources need to be refreshed in order to renew sas links. default is false
     */
    public function selectAllByUser(ObjectId $userId) :array{ //, bool $refresh=true) :array{
        $filter  = ['userId' => $userId];
		$options = ['typeMap'=>'Images'];
        $cursor  = $this->select($filter, $options)->toArray();
/*
        if ($refresh) {
            $cursor = $this->refreshSas($cursor, $this);
        }
*/
        return $cursor;
    }

}