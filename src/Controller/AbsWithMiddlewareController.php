<?php
declare(strict_types=1);

namespace App\Controller;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbsWithMiddlewareController {
    /**
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

    protected abstract function execRequest($request);
    protected abstract function execResponse($response);

    public function execute(ServerRequestInterface $request) :void {
        $resultingRequest = $request;

        foreach ($this->pipeline['request'] AS $middleware) {
            $resultingRequest = $middleware->handle($resultingRequest);
        }

        $response = $this->execRequest($resultingRequest);

        foreach ($this->pipeline['response'] AS $middleware) {
            $response = $middleware->handle($response);
        }

        $response = $this->execResponse($response);
    }
}