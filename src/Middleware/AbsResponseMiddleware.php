<?php

declare(strict_types=1);

namespace App\Middleware;

use App\Middleware\IMiddleware;
use Psr\Http\Message\ResponseInterface;

abstract class AbsResponseMiddleware implements IMiddleware{

    protected abstract function middlewareAction(ResponseInterface $response);

    public function handle($data, ?callable $next=null) {
        /*
        $result = $this->middlewareAction($data);

        if (is_null($next)) {
            return $result;
        }

        return $next($result);
        */
        if (! is_null($next)) {
            $data = $next($data);
        }
        
        return $this->middlewareAction($data);
    }
}