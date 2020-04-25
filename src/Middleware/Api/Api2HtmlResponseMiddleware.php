<?php

declare(strict_types=1);

namespace App\Middleware\Api;

use App\Middleware\IMiddleware;
use App\Middleware\AbsResponseMiddleware;
use Psr\Http\Message\ResponseInterface;
use App\Helper\ResponseFactory;

class Api2HtmlResponseMiddleware extends AbsResponseMiddleware implements IMiddleware{
    protected $responseFactory;
    public function __construct(ResponseFactory $responseFactory){
        $this->responseFactory = $responseFactory;
    }

    protected function middlewareAction (ResponseInterface $response) {
        try {
            $headers = $response->getHeaders();
            $headers['Content-Type'] = 'text/html';
            return $this->responseFactory->createResponse(
                ResponseFactory::HTML,
                $response->getStatusCode(), 
                $response->getBody()->read(1024),
                $headers
            );
        } catch (\InvalidArgumentException $e) {
            return $this->responseFactory->createResponse(
                ResponseFactory::HTML,
                400, 
                'Invalid arguments. Cannot create a valid response.'
            );
        }
    }
}