<?php
declare(strict_types=1);

namespace App\Middleware;
// use Psr\Http\Message\ResponseInterface;

interface IHttpResponseMiddleware {
    public function handle(string $response, ?callable $next);
}