<?php

declare(strict_types=1);

namespace App\Api;

use App\Config\Env;
use App\Helper\ResponseFactory;
use App\Db\BaseMapObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;


abstract class AbsApi {
    protected $responseFactory;
    protected $headers;

    public function __construct() {
        $this->cookieParam = Env::get('HTTP_COOKIE_PARAM');
        $this->config  = Env::get('API_CONFIG');
        $this->headers = [
            'Access-Control-Allow-Origin' => Env::get('ALLOW_ORIGIN'),
            'Content-Type' => 'application/json; charset=UTF-8',
            'Access-Control-Allow-Methods' => 'POST',
            'Access-Control-Max-Age' => '3600',
            'Access-Control-Allow-Headers' => 'Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With'
        ];
    }

    /**
     * int code status code
     * string message  Text message that will be in the response payload
    */
    protected function setResponse(int $code, $message, array $headers) :ResponseInterface {
        // set response code
        // http_response_code($code);
        // display message:
        // return json_encode(array("code" => $code, "message" => $message));
        try {
            return $this->responseFactory->createResponse(
                ResponseFactory::JSON,
                $code, 
                $message,
                $headers
            );
        } catch (\InvalidArgumentException $e) {
            return $this->responseFactory->createResponse(
                ResponseFactory::JSON,
                400, 
                'Invalid arguments. Cannot create a valid response.'
            );
        }
    }

 
    public abstract function execute(ServerRequestInterface $jsonData) :ResponseInterface;
}