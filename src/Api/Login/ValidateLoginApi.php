<?php

namespace App\Api\Login;

use \Firebase\JWT\JWT;
use App\Db\MongoConnection;
use App\Api\AbsApi;
use App\Db\User;
use App\Db\UserDbCollection;
use App\Helper\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class ValidateLoginApi {
    protected $config;
    protected $userDb;

	public function __construct(
        UserDbCollection $userDb, 
        ResponseFactory $responseFactory
	) {
        $this->userDb = $userDb;
		$this->responseFactory = $responseFactory;
		
		chdir(dirname(__DIR__, 3));
		$this->config = require_once 'config/api.php';
		date_default_timezone_set($this->config['timezone']);
    }
    
    
    public function execute(ServerRequestInterface $request) :ResponseInterface {
        $headers = $this->config['headers'];
        
        // get posted data
        if (! $request->hasHeader('Authorization')) {
            return $this->setResponse(400, 'Bad request.', $headers);
        }

        $jwt = $request->getHeader('Authorization');
        if (strpos($jwt, 'Bearer ') === false){
            return $this->setResponse(400, 'Bad request.', $headers);
        }

        $jwt = str_replace('Bearer ', '', $jwt);

        try {
            $decoded = JWT::decode($jwt, $key, array('HS256')); // decode jwt
            return $this->setResponse(200, 'Access granted.', $headers);
        } catch (\Exception $e) {
            return $this->setResponse(401, $e->getMessage(), $headers);
        }
    }
}
?>