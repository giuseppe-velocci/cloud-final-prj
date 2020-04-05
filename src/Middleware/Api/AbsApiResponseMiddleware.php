<?php

declare(strict_types=1);

namespace App\Middleware\Api;

// use App\Middleware\Interfaces\IJsonResponseMiddleware;
use App\Middleware\IMiddleware;
use Psr\Http\Message\ResponseInterface;

abstract class AbsApiResponseMiddleware implements IMiddleware{

    protected abstract function middlewareAction(ResponseInterface $response);

    public function handle($data, ?callable $next=null) {
        $result = $this->middlewareAction($data);

        if (is_null($next)) {
            return $result;
        }

        return $next($result);
    }
}