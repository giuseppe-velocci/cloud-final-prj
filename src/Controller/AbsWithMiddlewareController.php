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

    const REQUEST  = 'request';
    const RESPONSE = 'response';

    public function __construct (
        array $middlewares
    ) {
        foreach ($middlewares AS $middleware) {
            $this->pipeline[$this->getMiddlewareType($middleware)][] = $middleware;
        }
    }

    protected function getMiddlewareType($middleware) :string {
        $class = new \ReflectionClass($middleware->getMiddleware());
        $parent = $class->getParentClass()->name;
        if (stripos($parent, self::REQUEST) !== false) {
            return self::REQUEST;
        }
        return self::RESPONSE;
    }


    protected abstract function exec($request);


    public function execute(ServerRequestInterface $request) :void {
        $resultingRequest = $request;

        foreach ($this->pipeline[self::REQUEST] AS $middleware) {
            $resultingRequest = $middleware->handle($resultingRequest);
        }

        $response = $this->exec($resultingRequest);

        foreach ($this->pipeline[self::RESPONSE] AS $middleware) {
            $response = $middleware->handle($response);
        }
    }
}