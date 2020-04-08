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

// required headers
//header("Access-Control-Allow-Origin: http://localhost/rest-api-authentication-example/");
/*
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, 
    Access-Control-Allow-Headers, Authorization, X-Requested-With");

// required to decode jwt
include_once 'config.php';
use \Firebase\JWT\JWT;

// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = $data->jwt;

if(isset($jwt)){
 
    // if decode succeed, show user details
    try {
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
 
        // set response code
        http_response_code(200);
 
        // show user details
        echo json_encode(array(
            "message" => "Access granted.",
            "data" => $decoded->data
        ));
 
    }
    catch (Exception $e){
 
        // set response code
        http_response_code(401);
     
        // tell the user access denied  & show error message
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}
else{
 
    // set response code
    http_response_code(401);
 
    // tell the user access denied
    echo json_encode(array("message" => "Access denied."));
}
*/
?>