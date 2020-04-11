<?php
declare(strict_types=1);

namespace App\Api\Login;

use \Firebase\JWT\JWT;
use App\Config\Env;
use App\Db\MongoConnection;
use App\Api\AbsApi;
use App\Db\User;
use App\Db\UserDbCollection;
use App\Helper\ResponseFactory;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


// date_default_timezone_set('Europe/Rome');
class LoginApi extends AbsApi {
	protected $config;
	protected $userDb;
	protected $cookieParam;

	public function __construct(
        UserDbCollection $userDb, 
        ResponseFactory $responseFactory
	) {
        $this->userDb = $userDb;
		$this->responseFactory = $responseFactory;

		try {
            $this->cookieParam = Env::get('HTTP_COOKIE_PARAM');
        } catch (\InvalidArgumentException $e) {
            die($e->getMessage());
        }
		
		chdir(dirname(__DIR__, 3));
		$this->config = require_once 'config/api.php';
		date_default_timezone_set($this->config['timezone']);
	}
	

	public function execute(ServerRequestInterface $request) :ResponseInterface {
		$headers = $this->config['headers'];
		
		// get posted data
        $post = $request->getParsedBody();
        if (! isset($post['json'])) {
            return $this->setResponse(400, 'Bad request.', $headers);
        }
        $data = json_decode($post['json']);

		// if email exists, check password
		if (! $this->userDb->findByEmail($data->email)) {
            return $this->setResponse(401, 'Wrong credentials.', $headers);
		}

		if(! password_verify($data->pwd, $this->userDb->mapObj->getPassword())) {
			return $this->setResponse(400, 'Wrong credentials.', $headers);
		}

		$token = array(
			"iss" => $this->config['iss'],
			"aud" => $this->config['aud'],
			"iat" => $this->config['iat'],
			"nbf" => $this->config['nbf'],
			"data" => array(
				"id" => $this->userDb->mapObj->getId(),
				"firstname" => $this->userDb->mapObj->getFirstname(),
				"lastname" => $this->userDb->mapObj->getLastname(),
				"email" => $this->userDb->mapObj->getEmail(),
			)
		);

		// generate jwt
		$jwt = JWT::encode($token, $this->config['key']);
		$user = $this->userDb->mapObj->getEmail();
		$headers['Authorization'] = sprintf('Bearer %s', $jwt);
		
		return $this->setResponse(
			200, 
			['msg'=>"Successful login!", $this->cookieParam=>['user' => $user, 'token'=>$jwt]], 
			$headers
		);
	}
}
?>