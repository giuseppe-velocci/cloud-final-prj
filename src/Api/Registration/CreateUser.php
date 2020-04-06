<?php

declare(strict_types=1);

namespace App\Api\Registration;

use App\Api\AbsApi;
use App\Db\User;
use App\Db\UserDbCollection;
use App\Helper\ResponseFactory;
use Psr\Http\Message\ResponseInterface;


class CreateUser extends AbsApi{
	protected $config;
    protected $user;
    protected $userDb;

    public function __construct(
        User $user, 
        UserDbCollection $userDb, 
        ResponseFactory $responseFactory
    ) {
        // get database connection
        $this->user = $user;
        $this->userDb = $userDb;
        $this->responseFactory = $responseFactory;

        chdir(dirname(__DIR__, 3));
		$this->config = require_once 'config/api.php';
		date_default_timezone_set($this->config['timezone']);
    }

    public function execute(string $jsonData) :ResponseInterface {
        // required headers
        $headers = $this->config['headers'];

        // get posted data
        $data = json_decode($jsonData);
        
        if (
            is_null($data->firstname)
            || is_null($data->lastname)
            || is_null($data->email)
            || is_null($data->pwd)
        ) {
         // echo
            return $this->setResponse(400, 'Missing data. All parameters are needed.', $headers);
        }

        // instantiate user object
        $this->user->setFirstname($data->firstname);
        $this->user->setLastname($data->lastname);
        $this->user->setEmail($data->email);
        $this->user->setPassword($data->pwd);
      
        try {
            $this->userDb->mapObj = $this->user;
            $this->userDb->setupQuery('insert');
        } catch (\InvalidArgumentException $e) {
            return $this->setResponse(400, $e->getMessage(), $headers);
        }

        $code = 400;
        $message = 'Unable to create user.';
        if ($this->userDb->executeQueries()) {
            $code = 200;
            $message = 'User successfully created!';
        } 

        // message if unable to create user
  //  echo
        return $this->setResponse($code, $message, $headers);
    }
}