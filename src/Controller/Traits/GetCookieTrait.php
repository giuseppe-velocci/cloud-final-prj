<?php

declare(strict_types=1);

namespace App\Controller\Traits;

use Psr\Http\Message\ResponseInterface;
use App\Helper\CryptMsg;

trait GetCookieTrait {
    protected $toEncrypt = ['token', 'user'];

    protected function getLoginCookies (ResponseInterface $response):array {
        $cookies = $response->getCookieParams();
        foreach
    }
}