<?php
declare(strict_types=1);

namespace App\Db;

use App\Db\MongoWQuery;
use App\Helper\HashMsg;
use App\Helper\ISanitizer;
use App\Config\Env;
use App\Db\User;

class UserDbCollection extends BaseDbCollection {
    protected $collection = 'appusers';
   
    public function __construct(
        BaseMapObject $mapObj,
        MongoWQuery $wQuery,
        ISanitizer $sanitizer
	){
        parent::__construct(
            $mapObj, $wQuery, $sanitizer
        );
	}


	public function findByEmail(string $email) :bool {
		$filter = ['email'=>$this->sanitizer->clean($email)];
		$options = ['typeMap'=>'User'];
        $cursor = $this->select($filter, $options)->toArray();

		if(! empty($cursor)) {
            $this->mapObj = new User(
                $cursor[0]->_id,
                $cursor[0]->firstname,
                $cursor[0]->lastname,
                $cursor[0]->email,
                $cursor[0]->password
            );
            return true;
        } 
        return false;
	}
}
