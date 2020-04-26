<?php
declare(strict_types=1);

namespace App\Traits;

use Psr\Http\Message\ResponseInterface;

Trait SetMessageTrait {
    protected function setResultMessage(ResponseInterface $response) {
        setcookie(
            'message', 
            $response->getBody()->read(512),
            time() + 180,
            '',
            '',
            false,
            true
        );

        setcookie(
            'code', 
            ''.$response->getStatusCode(),
            time() + 180,
            '',
            '',
            false,
            true
        );
    }
}