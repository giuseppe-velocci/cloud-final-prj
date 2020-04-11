<?php

declare(strict_types=1);

namespace App\Traits;

use Psr\Http\Message\ResponseInterface;
use App\Helper\CryptMsg;
use App\Config\Env;

trait SetCookieTrait {

    protected function setLoginCookies (ResponseInterface $response) {
        try {
            $toEncrypt = explode(',', Env::get('COOKIES_TO_ENCRYPT'));
        } catch (\InvalidArgumentException $e) {
            die($e->getMessage());
        }

        $body = $response->getBody();
        $json = json_decode($body->getContents(), true);

        if ($json === null && json_last_error() !== JSON_ERROR_NONE) {
            return $response;
        }

        if (isset($json['storage'])) {
            $crypt = CryptMsg::instance();
            foreach($json['storage'] AS $name => $cookie) {
                 setcookie(
                    $name,
                    in_array($name, $toEncrypt) ? $crypt->encrypt($cookie, $crypt:: nonce()) : $cookie,
                    0,
                    '',
                    '',
                    false,
                    true
                );
            }
        }
        return $response;
    }
}