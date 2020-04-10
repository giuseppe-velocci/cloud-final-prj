<?php

declare(strict_types=1);

namespace App\Controller\Traits;

use Psr\Http\Message\ServerRequestInterface;
use App\Helper\CryptMsg;
use App\Config\Env;


trait GetCookieTrait {
    protected function getCookies (ServerRequestInterface $response):array {
        try {
            $toEncrypt = explode(',', Env::get('COOKIES_TO_ENCRYPT'));
        } catch (\InvalidArgumentException $e) {
            die($e->getMessage());
        }
        
        $crypt = CryptMsg::instance();
        $cookies = $response->getCookieParams();

        foreach($cookies AS $name => $value) {
            if (in_array($name, $toEncrypt)) {
                $cookies[$name] = $crypt->decrypt($value, $crypt:: nonce());
            }
        }

        return $cookies;
    }
}