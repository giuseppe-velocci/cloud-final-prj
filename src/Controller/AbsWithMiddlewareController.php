<?php
declare(strict_types=1);

namespace App\Controller;
use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;

abstract class AbsWithMiddlewareController {
    /**
     * Pipeline of operations. Be aware that request middleware execute
     * $next callback BEFORE thier logic, while response middleware
     * execute it AFTER their own logic.
     * All operations will be executed in the exact order they are declared in the constructor.
     * 
     * @access public
     * @var array $pipeline Array of arrays of InjectableMiddleware instances
     */
    protected $pipeline;

    public function __construct (
        array $requestMiddlewares,
        array $responseMiddlewares
    ) {
        $this->pipeline['request']  = $requestMiddlewares;
        $this->pipeline['response'] = $responseMiddlewares;
    }

    protected abstract function execRequest($request) :ResponseInterface;

    public function execute(ServerRequestInterface $request) :void {
        $resultingRequest = $request;

        foreach ($this->pipeline['request'] AS $middleware) {
            $resultingRequest = $middleware->handle($resultingRequest);
        }

        $response = $this->execRequest($resultingRequest);

        foreach ($this->pipeline['response'] AS $middleware) {
            $response = $middleware->handle($response);
        }
    }
}