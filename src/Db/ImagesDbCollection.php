<?php
declare(strict_types=1);

namespace App\Db;

use MongoDB\BSON\ObjectId;
use App\Db\MongoWQuery;
use App\Helper\ISanitizer;
use App\Config\Env;
use App\Img\Blob;

class ImagesDbCollection extends BaseDbCollection {
//    use \App\Traits\RefreshSasTrait;

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
     * @param bool  $minimumData Whether minimum amount of data should be returnd or not
     */
    public function selectAllByUser(ObjectId $userId, array $search=[], bool $minimumData=true) :array{ //, bool $refresh=true) :array{
        $filter  = ['userId' => $userId];
        $options = [
            'typeMap'=>'Images', 
            'sort' => ['_id' => -1]
        ];
        if ($minimumData) {
            $options['projection'] = [
                'shares' => 0,
                'exif' => 0
            ];
        }
        if (count($search) > 0) {
            $filter = array_merge($filter, $search);
        }

        $cursor  = $this->select($filter, $options)->toArray();
/*
        if ($refresh) {
            $cursor = $this->refreshSas($cursor, $this);
        }
*/
        return $cursor;
    }

}