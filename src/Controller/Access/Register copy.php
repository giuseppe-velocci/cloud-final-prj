<?php
declare(strict_types=1);

namespace App\Controller\Access;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Helper\HashMsg;
use App\Api\Registration\CreateUserApi;
use App\Middleware\Api\ApiPostRequestMiddleware;
use App\Middleware\Api\Api2HtmlResponseMiddleware;
use App\Middleware\Cookie\CookieMiddleware;

use App\Controller\AbsController;
use App\Middleware\InjectableMiddleware;

class Register extends AbsController{
    protected $createUser;

    public function __construct(
        CreateUserApi $createUser, 
        ApiPostRequestMiddleware $requestMiddleware,
        Api2HtmlResponseMiddleware $responseMiddleware
    )
    {
        $this->createUser = $createUser;

        $requestMiddleware = [
            new InjectableMiddleware($requestMiddleware,
                function ($request) {
                    $post = $request->getParsedBody();
                    $post['pwd'] = HashMsg::hash($post['pwd']);
                    return $request->withParsedBody($post);
                }
            ),
        ];

        $responseMiddleware = [
            new InjectableMiddleware($responseMiddleware,
                function($response) {
                    setcookie('message', $response->getBody()-> read(60));
                    return $response;
                }
            ),
        ];
        parent::__construct(
            $requestMiddleware, $responseMiddleware
        );
    }

    protected function execRequest($request) {
        $post = $request->getParsedBody();
        return $this->createUser->execute($post['json']);
    }

    protected function execResponse($response) {
        header('Location: /register');
        exit;
    }
}