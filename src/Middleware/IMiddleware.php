<?php
declare(strict_types=1);

namespace App\Middleware;

interface IMiddleware {
    public function handle($data, ?callable $next=null);
}