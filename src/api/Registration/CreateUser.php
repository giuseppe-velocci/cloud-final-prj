<?php

declare(strict_types=1);

namespace App\Registration;

use App\Db\MongoConnection;
use App\Db\User;

// required headers
//header("Access-Control-Allow-Origin: http://localhost/rest-api-authentication-example/");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, 
    Access-Control-Allow-Headers, Authorization, X-Requested-With");

// get database connection
$dbConnection = new MongoConnection();
 
// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// instantiate user object
$user = new User($dbConnection);

$user->setFirstname(htmlspecialchars(strip_tags($data->firstname)));
$user->setLastname(htmlspecialchars(strip_tags($data->lastname)));
$user->setEmail(htmlspecialchars(strip_tags($data->email)));
$user->setPassword(htmlspecialchars(strip_tags($data->password)));

if($user->AddUser()){
    // set response code
    http_response_code(200);
 
    // display message: user was created
    echo json_encode(array("message" => "User was created."));
}
// message if unable to create user
else{
 
    // set response code
    http_response_code(400);
 
    // display message: unable to create user
    echo json_encode(array("message" => "Unable to create user."));
}