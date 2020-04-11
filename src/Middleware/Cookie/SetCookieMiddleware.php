<?php

declare(strict_types=1);

namespace App\Middleware\Cookie;

use App\Middleware\IMiddleware;
use App\Middleware\AbsResponseMiddleware;
use Psr\Http\Message\ResponseInterface;
use App\Helper\CryptMsg;
use App\Config\Env;

class SetCookieMiddleware extends AbsResponseMiddleware implements IMiddleware{
    protected $crypt;
    protected $toEncrypt;
    const BODY_GET_COOKIE_PARAM = 'storage';

    public function __construct(CryptMsg $crypt){
        $this->crypt = $crypt;

        try {
            $this->toEncrypt = explode(',', Env::get('COOKIES_TO_ENCRYPT'));
        } catch (\InvalidArgumentException $e) {
            die($e->getMessage());
        }
    }

    protected function middlewareAction (ResponseInterface $response) {
        if ($response->getStatusCode() != 200) {
            return $response;
        }
        
        $body = $response->getBody();
        $json = json_decode($body->read(1024), true);
   

        if ($json === null && json_last_error() !== JSON_ERROR_NONE) {
            return $response;
        }

        if (isset($json[self::BODY_GET_COOKIE_PARAM])) {
            foreach($json[self::BODY_GET_COOKIE_PARAM] AS $name => $cookie) {
             //Set-Cookie: <cookie-name>=<cookie-value>; Domain=<domain-value>; Secure; HttpOnly
                setcookie(
                    $name,
                    in_array($name, $this->toEncrypt) ? $this->crypt->encrypt($cookie, $this->crypt:: nonce()) : $cookie,
                    0,
                    '',
                    '',
                    false,
                    true
                );
            }
        }
// var_dump('cookie');
// var_dump($body->read(1024));
        return $response;
    }
}