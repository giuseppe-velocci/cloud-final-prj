<?php

declare(strict_types=1);

namespace App\Middleware\Crypto;

use Psr\Http\Message\ServerRequestInterface;
use App\Middleware\IMiddleware;
use App\Middleware\AbsRequestMiddleware;
use App\Helper\HashMsg;

class HashMiddleware extends AbsRequestMiddleware implements IMiddleware{
    /**
     * @access protected
     * @var array $fieldsToBeHashed Array of strings with the name of the params that will be hashed 
     */
    protected $fieldsToBeHashed = ['pwd'];
    

    protected function middlewareAction (ServerRequestInterface $request) {
        // will only look inside POST data?
        $post = $request->getParsedBody();
        foreach ($this->fieldsToBeHashed AS $key) {
            if (isset($post[$key])) {
                $post[$key] = HashMsg::hash($post[$key]);
            }
        }
        return $request->withParsedBody($post);
    }
}