<?php

declare(strict_types=1);

namespace App\Middleware\Cookie;

use App\Middleware\IMiddleware;
use App\Middleware\AbsRequestMiddleware;
use Psr\Http\Message\ServerRequestInterface;
use App\Helper\CryptMsg;
use App\Config\Env;

class GetCookieMiddleware extends AbsRequestMiddleware implements IMiddleware{
    protected $crypt;
    protected $toEncrypt;
    protected $httpCookieParam;

    public function __construct(CryptMsg $crypt){
        $this->crypt = $crypt;

        try {
            $this->toEncrypt = explode(',', Env::get('COOKIES_TO_ENCRYPT'));
        } catch (\InvalidArgumentException $e) {
            die($e->getMessage());
        }
    }

    protected function middlewareAction (ServerRequestInterface $request) {
        $cookies = $request->getCookieParams();

        foreach($cookies AS $name => $value) {
            if (in_array($name, $this->toEncrypt)) {
                $cookies[$name] = $crypt->decrypt($value, $this->crypt:: nonce());
            }
        }

        return $request;
    }
}