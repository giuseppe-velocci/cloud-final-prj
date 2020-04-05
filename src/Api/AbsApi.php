<?php

declare(strict_types=1);

namespace App\Api;

use App\Helper\ResponseFactory;
use Psr\Http\Message\ResponseInterface;

abstract class AbsApi {
    protected $responseFactory;

    /**
     * int code status code
     * string message  Text message that will be in the response payload
    */
    protected function setResponse(int $code, string $message, array $headers) :ResponseInterface {
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
 
    public abstract function execute(string $jsonData) :ResponseInterface;
}