<?php

namespace App\Api\Login;

// required headers
header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: POST");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, 
    Authorization, X-Requested-With");
 
// required to decode jwt
include_once 'config.php';
use \Firebase\JWT\JWT;

// get database connection and create user
$dbConnection = new MongoConnection();
$user = new User($dbConnection);

// get posted data
$data = json_decode(file_get_contents("php://input"));
 
// get jwt
$jwt = $data->jwt;

if(isset($jwt)){
    // if decode succeed, show user details
    try {
 
        // decode jwt
        $decoded = JWT::decode($jwt, $key, array('HS256'));
 
        // set user property values
        $user->id = $data->_id;
        $user->firstname = $data->firstname;
        $user->lastname = $data->lastname;
        $user->email = $data->email;
        $user->password = $data->password;

        if($user->update()){
            // we need to re-generate jwt because user details might be different
            $token = array(
                "iss" => $iss,
                "aud" => $aud,
                "iat" => $iat,
                "nbf" => $nbf,
                "data" => array(
                    "id" => $user->id,
                    "firstname" => $user->firstname,
                    "lastname" => $user->lastname,
                    "email" => $user->email
                )
            );
            $jwt = JWT::encode($token, $key);
            
            // set response code
            http_response_code(200);
            
            // response in json format
            echo json_encode(
                array(
                    "message" => "User was updated.",
                    "jwt" => $jwt
                )
            );
        }
         
        // message if unable to update user
        else{
            // set response code
            http_response_code(401);
         
            // show error message
            echo json_encode(array("message" => "Unable to update user."));
        }
    }
 // if decode fails, it means jwt is invalid
    catch (Exception $e){
    
        // set response code
        http_response_code(401);
    
        // show error message
        echo json_encode(array(
            "message" => "Access denied.",
            "error" => $e->getMessage()
        ));
    }
}else{
 
    // set response code
    http_response_code(401);
 
    // tell the user access denied
    echo json_encode(array("message" => "Access denied."));
}