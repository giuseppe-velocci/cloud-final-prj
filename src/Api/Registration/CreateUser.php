<?php

declare(strict_types=1);

namespace App\Api\Registration;

use App\Db\User;
use App\Db\UserDbCollection;
use App\Api\AbsApi;

class CreateUser extends AbsApi{
    protected $user;
    protected $userDb;

    public function __construct(User $user, UserDbCollection $userDb) {
        // get database connection
        $this->user = $user;
        $this->userDb = $userDb;
    }

    public function execute(string $jsonData) :string {
        // required headers
/**/         //header('Access-Control-Allow-Origin: http://localhost/rest-api-authentication-example/');
        header('Content-Type: application/json; charset=UTF-8');
        header('Access-Control-Allow-Methods: POST');
        header('Access-Control-Max-Age: 3600');
        header('Access-Control-Allow-Headers: Content-Type, '
            .'Access-Control-Allow-Headers, Authorization, X-Requested-With');

        // get posted data
        //$data = $request->getParsedBody();
        $data = json_decode($jsonData); // json_decode(file_get_contents('php://input')));
        
        if (
            is_null($data->firstname)
            || is_null($data->lastname)
            || is_null($data->email)
            || is_null($data->pwd)
        ) {
         // echo
            return $this->setResponse(400, 'Missing data. All parameters are needed.'); ;
        }

        // instantiate user object
        $this->user->setFirstname($data->firstname);
        $this->user->setLastname($data->lastname);
        $this->user->setEmail($data->email);
        $this->user->setPassword($data->pwd);
/*
var_dump($this->user);
die();
*/
        
        try {
            $this->userDb->mapObj = $this->user;
            $this->userDb->setupQuery('insert');
        } catch (\InvalidArgumentException $e) {
           // echo
            return  $this->setResponse(400, $e->getMessage());;
        }

        $code = 400;
        $phrase = 'Unable to create user.';
        if (! $this->userDb->executeQueries()) {
            $code = 200;
            $phrase = 'User was created.';
        } 

        // message if unable to create user
  //  echo
        return $this->setResponse($code, $phrase);;
    }
}