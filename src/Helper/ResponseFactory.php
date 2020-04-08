<?php
declare(strict_types=1);

namespace App\Helper;

use Psr\Http\Message\ResponseInterface;
use Zend\Diactoros\Response\HtmlResponse;
use Zend\Diactoros\Response\JsonResponse;

class ResponseFactory {
    const HTML = 0;
    const JSON = 1;

    public function createResponse(
        int $type, 
        int $code, 
        $body, 
        array $headers=[]
    ) :ResponseInterface {
        if ($type == self::HTML) {
            return new \Zend\Diactoros\Response\HtmlResponse(
                $body,
                $code,
                $headers 
            );
        } elseif ($type == self::JSON) {
            return new \Zend\Diactoros\Response\JsonResponse(
                $body,
                $code,
                $headers 
            );
        }
    }
}