<?php

declare(strict_types=1);

namespace App\Middleware;
use Psr\Http\Message\ResponseInterface;

class ApiResponseMiddleware implements IHttpResponseMiddleware{
    protected $defaultCallback;

    public function __construct(callable $defaultCallback) {
        $this->defaultCallback = $defaultCallback;
    }

    public function handle(string $response, ?callable $next) {
        if (is_null($next)) {
            $next = $this->defaultCallback;
        }

        return $next($response);
    }
}