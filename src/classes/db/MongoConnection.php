<?php
declare(strict_types=1);

namespace App\Db;

use App\Config\Env;

class MongoConnection {
    // format = "mongodb://alex:mypassword@10.111.0.2:27017/"
    private $connection;

    public function __construct() {
        $connectionString = 'mongodb://' . 
        Env::get('DB_USER') . ':' . Env::get('DB_PASSWORD')
        . '@' . preg_replace('/http(s?):\/\//', '', Env::get('DB_HOST'));

        // connect to a host at a given port 
        $this->connection = new MongoDB\Driver\Manager($connectionString);
    }

// var_dump($connection);


    public function setCurrentCollection(string $db, string $collection) : MongoDB\Driver\Manager {
        $dbLink;
        try {
            $dbLink = $this->connection->$db->$collection;
        } catch (MongoDB\Driver\Exception\ConnectionException $e) { // check exception type
            throw $e;
        }
        return $dbLink;
    }


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