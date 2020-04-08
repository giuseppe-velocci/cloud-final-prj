<?php
declare(strict_types=1);

namespace App\Controller\Traits;

use Psr\Http\Message\ServerRequestInterface;
use App\Helper\CryptMsg;

Trait GetUserTrait {
    
    protected function findUser(ServerRequestInterface $request): ?string {
        $cookies = $request->getCookieParams();
        if (! isset($cookies['user'])) {
            return null;
        }

        $crypt = CryptMsg::instance();
        return $crypt->decrypt($cookies['user'], $crypt::nonce());
    }
}