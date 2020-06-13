<?php
declare(strict_types=1);

namespace App\Db;

use App\Config\Env;

class MongoConnection {
    // format = "mongodb://localhost:27017"
    // format = "mongodb://alex:mypassword@10.111.0.2:27017/"
    private $connectionString;
    private $db;

    /**
     * 
     */
    public function __construct(?string $user=null, ?string $pwd=null) {
        try {
            $user = is_null($user) ? Env::get('DB_USER') : $user;
            $pwd  = is_null($pwd)  ? Env::get('DB_PWD') : $pwd;
            $host = Env::get('DB_HOST');
            $this->db = Env::get('DB_NAME');

        } catch (\InvalidArgumentException $e) {
            die($e->getMessage());
        }

        $connectionString = "mongodb://";
        if (strlen($user) > 0 && strlen($pwd) > 0)
            $connectionString .= $user . ':' . $pwd. '@';
        
        $connectionString .= preg_replace('/http(s?):\/\//', '', $host);
        $connectionString .= sprintf('/%s', $this->db);

        $this->connectionString = $connectionString;
    }


    public function getDb() {
        return $this->db;
    }
    

    public function getConnection() : \MongoDB\Driver\Manager {
        try {
            $connection = new \MongoDB\Driver\Manager($this->connectionString);

        } catch (\MongoDB\Driver\Exception\ConnectionException $e) { // check exception type
            die($e->getMessage());

        } catch(\MongoDB\Driver\Exception\AuthenticationException $e) {
            die($e->getMessage());
        }

        return $connection;
    }
}