<?php

declare(strict_types=1);

namespace App\Middleware\Api;

// use App\Middleware\Interfaces\IHttpRequestMiddleware;
use App\Middleware\IMiddleware;
use Psr\Http\Message\ServerRequestInterface;

abstract class AbsApiRequestMiddleware implements IMiddleware{
    protected abstract function middlewareAction(ServerRequestInterface $request);

    public function handle($data, ?callable $next=null) {
        $result = $this->middlewareAction($data);

        if (is_null($next)) {
            return $result;
        }

        return $next($result);
    }
}