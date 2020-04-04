<?php
declare(strict_types=1);

namespace App\Db;

use App\Db\MongoWQuery;
use App\Helper\ISanitizer;
use App\Config\Env;

class ImagesDbCollection extends AbsDbCollection{
    
    public function __construct(
        BaseMapObject $mapObj,
        MongoWQuery $wQuery,
        ISanitizer $sanitizer,
        array $requiredMapObjParams = ['url', 'userId', 'tags'],
        string $collection = 'images'
	){
        parent::__construct(
            $mapObj, $wQuery, $sanitizer, $requiredMapObjParams, $collection
        );
	}

}