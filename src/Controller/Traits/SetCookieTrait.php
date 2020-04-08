<?php

declare(strict_types=1);

namespace App\Controller\Traits;

use Psr\Http\Message\ResponseInterface;
use App\Helper\CryptMsg;

trait SetCookieTrait {
    protected $toEncrypt = ['token', 'user'];

    protected function setLoginCookies (ResponseInterface $response) {
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
                    in_array($name, $this->toEncrypt) ? $crypt->encrypt($cookie, $crypt:: nonce()) : $cookie,
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