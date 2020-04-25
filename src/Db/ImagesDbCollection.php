<?php
declare(strict_types=1);

namespace App\Db;

use MongoDB\BSON\ObjectId;
use App\Db\MongoWQuery;
use App\Helper\ISanitizer;
use App\Config\Env;
use App\Img\Blob;

class ImagesDbCollection extends BaseDbCollection {
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
     * @param array $search An array with search params
     * @param bool  $refresh Tells if resources need to be refreshed in order to renew sas links. default is false
     */
    public function selectAllByUser(ObjectId $userId, array $search=[], bool $refresh=true) :array{ //, bool $refresh=true) :array{
        $filter  = ['userId' => $userId];
        $options = [
            'typeMap'=>'Images', 
            'projection' => [
                'tags' => 0,
                'exif' => 0
            ],
            'sort' => ['>id' => -1]
        ];
        if (count($search) > 0) {
            $filter = array_merge($filter, $search);
        }

        $cursor  = $this->select($filter, $options)->toArray();

        if ($refresh) {
            $cursor = $this->refreshSas($cursor, $this);
        }

        return $cursor;
    }

}