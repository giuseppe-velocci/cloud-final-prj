<?php

declare(strict_types=1);

namespace App\Api\Login;

use App\Db\MongoConnection;
use App\Db\User;
use \Firebase\JWT\JWT;

include_once 'config.php';

// required headers
//header("Access-Control-Allow-Origin: http://localhost/rest-api-authentication-example/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

// get database connection and create user
$dbConnection = new MongoConnection();
$user = new User($dbConnection);

// get posted data
$data = json_decode(file_get_contents("php://input"));

// if email exists, check password
if($user->EmailExists(htmlspecialchars(strip_tags($data->email))) && password_verify($data->password, $user->password)){

	$token = array(
		"iss" => $iss,
		"aud" => $aud,
		"iat" => $iat,
		"nbf" => $nbf,
		"data" => array(
			"id" => $user->id,
			"firstname" => $user->firstname,
			"lastname" => $user->lastname,
			"email" => $user->email,
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

?>