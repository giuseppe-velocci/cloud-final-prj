<?php
declare(strict_types=1);

namespace App\Db;

use App\Db\MongoWQuery;
use App\Helper\ISanitizer;
use App\Config\Env;

class ShareDbCollection extends BaseDbCollection {
    protected $collection = 'appusers';
   
    public function __construct(
        Share $mapObj,
        MongoWQuery $wQuery,
        ISanitizer $sanitizer
	){
        parent::__construct(
            $mapObj, $wQuery, $sanitizer
        );
	}


	public function findByGuid(string $urlGuid) :bool {
        $filter  = ['urlGuid'=>$this->sanitizer->clean($urlGuid)];
		$options = ['typeMap'=>'Share'];
        $cursor = $this->select($filter, $options)->toArray();

		if(! empty($cursor)) {
            $this->mapObj = new User(
                $cursor[0]->_id,
                $cursor[0]->imgUrl,
                $cursor[0]->urlGuid,
                $cursor[0]->email,
                $cursor[0]->expiry
            );
            return true;
        } 
        return false;
	}
}
