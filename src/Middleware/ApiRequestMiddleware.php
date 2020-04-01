<?php

declare(strict_types=1);

namespace App\Middleware;
use Psr\Http\Message\ServerRequestInterface;

class ApiRequestMiddleware implements IHttpRequestMiddleware{
    protected $defaultCallback;

    public function __construct(callable $defaultCallback) {
        $this->defaultCallback = $defaultCallback;
    }

    public function handle(ServerRequestInterface $request, ?callable $next) {
        if (is_null($next)) {
            $next = $this->defaultCallback;
        }

        return $next($request->getParsedBody());
    }
}