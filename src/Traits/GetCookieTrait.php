<?php

declare(strict_types=1);

namespace App\Traits;

use Psr\Http\Message\ServerRequestInterface;
use App\Helper\CryptMsg;
use App\Config\Env;


trait GetCookieTrait {
    protected function getCookies (ServerRequestInterface $request):array {
        try {
            $toEncrypt = explode(',', Env::get('COOKIES_TO_ENCRYPT'));
        } catch (\InvalidArgumentException $e) {
            die($e->getMessage());
        }
        
        $crypt = CryptMsg::instance();
        $cookies = $request->getCookieParams();

        foreach($cookies AS $name => $value) {
            if (in_array($name, $toEncrypt)) {
                $cookies[$name] = $crypt->decrypt($value, $crypt:: nonce());
            }
        }

        return $cookies;
    }
}