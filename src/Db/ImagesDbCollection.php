<?php
declare(strict_types=1);

namespace App\Db;

use MongoDB\BSON\ObjectId;
use App\Db\MongoWQuery;
use App\Helper\ISanitizer;
use App\Config\Env;
use App\Img\Blob;

class ImagesDbCollection extends BaseDbCollection{
    use \App\Traits\RefreshSasTrait;

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
     */
    public function selectAllByUser(ObjectId $userId) :array{
        $filter  = ['userId' => $userId];
		$options = ['typeMap'=>'Images'];
        $cursor  = $this->select($filter, $options)->toArray();

        $this->refreshSas($cursor, $this);

        return $cursor;
    }

}