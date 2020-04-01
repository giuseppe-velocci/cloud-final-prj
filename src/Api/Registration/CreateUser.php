<?php

declare(strict_types=1);

namespace App\Api\Registration;

use App\Db\MongoConnection;
use App\Db\User;
use App\Api\AbsApi;

class CreateUser extends AbsApi{

    protected $dbConnection;
    protected $user;

    public function __construct(MongoConnection $dbConnection, User $user) {
        // get database connection
        $this->dbConnection = $dbConnection;
        $this->user = $user;
    }

    public function execute(string $jsonData) :string {
        // required headers
        //header('Access-Control-Allow-Origin: http://localhost/rest-api-authentication-example/');
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Max-Age: 3600');
        header('Access-Control-Allow-Headers: Content-Type, '
            .'Access-Control-Allow-Headers, Authorization, X-Requested-With');

        // get posted data
        //$data = $request->getParsedBody();
        $data = json_decode($jsonData); // json_decode(file_get_contents('php://input')));
        
        if (strlen($data) == 0) {
           return $this->setResponse(400, 'Empty data given.');
        }

        // instantiate user object
        $this->user->setFirstname(htmlspecialchars(strip_tags($data->firstname)));
        $this->user->setLastname(htmlspecialchars(strip_tags($data->lastname)));
        $this->user->setEmail(htmlspecialchars(strip_tags($data->email)));
        $this->user->setPassword(htmlspecialchars(strip_tags($data->password)));

        if($this->user->AddUser()){
            return $this->setResponse(200, 'User was created.');
        }
        // message if unable to create user
        return $this->setResponse(400, 'Unable to create user.');
    }
}