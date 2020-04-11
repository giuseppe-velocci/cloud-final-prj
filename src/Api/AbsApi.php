<?php

declare(strict_types=1);

namespace App\Api;

use App\Helper\ResponseFactory;
use App\Db\BaseMapObject;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbsApi {
    protected $responseFactory;

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