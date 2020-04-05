<?php
declare(strict_types=1);

namespace App\Api\Login;

use \Firebase\JWT\JWT;
use App\Db\MongoConnection;
use App\Api\AbsApi;
use App\Db\User;
use App\Db\UserDbCollection;
use App\Helper\ResponseFactory;
use Psr\Http\Message\ResponseInterface;


// date_default_timezone_set('Europe/Rome');
class Login extends AbsApi {
	protected $config;
	protected $user;
    protected $userDb;

	public function __construct(
		User $user, 
        UserDbCollection $userDb, 
        ResponseFactory $responseFactory
	) {
		$this->user = $user;
        $this->userDb = $userDb;
		$this->responseFactory = $responseFactory;
		
		chdir(dirname(__DIR__, 3));
		$this->config = require_once 'config/api.php';
		date_default_timezone_set($this->config['timezone']);
	}
	
	public function execute(string $jsonData) :ResponseInterface {
		// required headers
		//header("Access-Control-Allow-Origin: http://localhost/rest-api-authentication-example/");
/*
		header("Content-Type: application/json; charset=UTF-8");
		header("Access-Control-Allow-Methods: POST");
		header("Access-Control-Max-Age: 3600");
		header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");
*/
		$headers = $this->config['headers'];
		

		// get posted data
		// $data = json_decode(file_get_contents("php://input"));
		$data = json_decode($jsonData);

		// if email exists, check password
		if(
			$userDb->emailExists($data->getEmail()) 
			&& password_verify($data->pwd, $user->getPassword())
		){
			$token = array(
				"iss" => $this->config['iss'],
				"aud" => $this->config['aud'],
				"iat" => $this->config['iat'],
				"nbf" => $this->config['nbf'],
				"data" => array(
					"id" => $user->getId(),
					"firstname" => $user->getFirstname(),
					"lastname" => $user->getLastname(),
					"email" => $user->getEmail(),
				)
			);

		// set response code
		http_response_code(200);

		// generate jwt
			$jwt = JWT::encode($token, $key);
			echo json_encode(
				array(
					'message' => "Successful login.",
					'jwt' => $jwt
				)
			);
		}
		// login failed
		else
		{
		// set reponse code
			http_response_code(401);

		// tell the user login failed
			echo json_encode(array("message" => "Login failed."));
		}
	}
}
?>