<?php

declare(strict_types=1);

namespace App\Middleware\Cookie;

use App\Middleware\IMiddleware;
use App\Helper\ISanitizer;
use App\Helper\CryptMsg;
use Psr\Http\Message\ServerRequestInterface;

class EncryptMiddleware implements IMiddleware {
    protected $valuesToEncrypt;
    protected $source;

    public function __construct(
        $source,
        $valuesToEncrypt
    ) {
        $this->source = $source;
        $this->valuesToEncrypt = $valuesToEncrypt;
    }


    public function handle($data, ?callable $next=null){

    }
}