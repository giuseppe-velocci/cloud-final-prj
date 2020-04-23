<?php

declare(strict_types=1);

namespace App\Api\Registration;

use App\Api\AbsApi;
use App\Config\Env;
use App\Db\User;
use App\Db\UserDbCollection;
use App\Helper\ResponseFactory;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;


class CreateUserApi extends AbsApi{
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

        try {
			parent::__construct();
			
        } catch (\InvalidArgumentException $e) {
            die($e->getMessage());
        }

        date_default_timezone_set($this->config['timezone']);
    }



    public function execute(ServerRequestInterface $request) :ResponseInterface {
        // required headers
        $headers = $this->headers;

        // get posted data
        $post = $request->getParsedBody();
        if (! isset($post['json'])) {
            return $this->setResponse(400, 'Bad request.', $headers);
        }
        
        $data = json_decode($post['json']);
       
        if ($this->userDb->findByEmail($data->email)) {
            return $this->setResponse(400, 'A user with these credentials is already registered.', $headers);
        }

        if (
            is_null($data->firstname)
            || is_null($data->lastname)
            || is_null($data->email)
            || is_null($data->pwd)
        ) {
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
        return $this->setResponse($code, $message, $headers);
    }
}