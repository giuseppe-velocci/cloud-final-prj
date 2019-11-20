<?php
declare(strict_types=1);

namespace App\Db;

use App\Config\Env;

class MongoConnection {
    // format = "mongodb://localhost:27017"
    // format = "mongodb://alex:mypassword@10.111.0.2:27017/"
    private $connectionString;

    /**
     * 
     */
    public function __construct(?string $user=null, ?string $pwd=null) {
        try {
            $user = is_null($user) ? Env::get('DB_USER') : $user;
            $pwd  = is_null($pwd)  ? Env::get('DB_PWD') : $pwd;
            $host = Env::get('DB_HOST');
        } catch (\MongoDB\Driver\Exception\InvalidArgumentException $e) {
            die($e->getMessage());
        } catch ( \MongoDB\Driver\Exception\RuntimeException $e) {
            die($e->getMessage());
        }

        $connectionString = "mongodb://";
        if (strlen($user) > 0 && strlen($pwd) > 0)
            $connectionString .= $user . ':' . $pwd. '@';
        $connectionString .= preg_replace('/http(s?):\/\//', '', $host);

        $this->connectionString = $connectionString;
    }

    public function setCurrentDb(string $db) : \MongoDB\Driver\Manager {
        try {
            $connection = new \MongoDB\Driver\Manager($this->connectionString);
            $dbLink = $connection->$db;
        } catch (\MongoDB\Driver\Exception\ConnectionException $e) { // check exception type
            throw $e;
        }
        return $dbLink;
    }

    public function setCurrentCollection(string $db, string $collection) : \MongoDB\Driver\Manager {
        try {
            $connection = new \MongoDB\Driver\Manager($this->connectionString);
            $dbLink = $connection->$db->$collection;
        } catch (\MongoDB\Driver\Exception\ConnectionException $e) { // check exception type
            throw $e;
        }
        return $dbLink;
    }

    // https://stackoverflow.com/questions/53019846/undefined-property-mongodb-driver-managerdb

/*
// Manager Class
$manager = new MongoDB\Driver\Manager($connection);

// Query Class
$query = new MongoDB\Driver\Query(array('age' => 30));

// Output of the executeQuery will be object of MongoDB\Driver\Cursor class
$cursor = $manager->executeQuery('testDb.testColl', $query);

// Convert cursor to Array and print result
print_r($cursor->toArray());
*/
}
// https://stackoverflow.com/questions/40971613/class-mongodb-client-not-found-mongodb-extension-installed