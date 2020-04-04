<?php

declare(strict_types=1);

namespace App\Middleware\Api;

// use Zend\Diactoros\ResponseFactory;
use Psr\Http\Message\ResponseInterface;

class Api2HttpResponseMiddleware extends AbsApiResponseMiddleware{
/*    protected $responseFactory;
    public function __construct(ResponseFactory $responseFactory){
        $this->responseFactory = $responseFactory;
    }
*/
    protected function middlewareAction (string $response) {
        $jsonObj = json_decode($response);
        try {
            /* return $this->responseFactory->createResponse(
                $jsonObj->code 
                // ,$jsonObj->message
            );
            */
            return new \Zend\Diactoros\Response\HtmlResponse(
                $jsonObj->message,
                $jsonObj->code 
            );
        } 
        catch (\InvalidArgumentException $e) 
        {
            return $this->responseFactory->createResponse(500, $e);
        }
    }
}