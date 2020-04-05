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


	public function emailExists(string $email): bool{
		$filter = ['email'=>$email];
		$options = ['typeMap'=>'User'];
		$query = new Query($filter, $options);

		$cursor = $this->connection->executeQuery(
            sprintf("%s.%s", $this->wQuery->getDb(), $this->collection)
            , $query
        );

		if(!empty($cursor))
		{
            array_map($this->sanitizer->clean, $cursor);

            $this->user = new User(
                $cursor['_id'],
                $cursor['firstname'],
                $cursor['lastname'],
                $cursor['email'],
                $cursor['password']
            );
			
			return true;
		}

		return false;
	}
}
