<?php
declare(strict_types=1);

namespace App\Controller\Access;

use Psr\Http\Message\ServerRequestInterface;
use Psr\Http\Message\ResponseInterface;
use App\Api\Registration\CreateUser;
use App\Middleware\Api\ApiPostRequestMiddleware;
use App\Middleware\Api\Api2HttpResponseMiddleware;
use App\Middleware\Cookie\CookieMiddleware;

use App\Controller\AbsWithMiddlewareController;
use App\Middleware\InjectableMiddleware;

class Register extends AbsWithMiddlewareController{
    protected $createUser;
    protected $request2JsonMiddleware;
    protected $json2HttpResponseMiddleware;
    protected $cookieMiddleware;


    public function __construct(
        CreateUser $createUser, 
        ApiPostRequestMiddleware $request2JsonMiddleware,
        Api2HttpResponseMiddleware $json2HttpResponseMiddleware
 //       ,CookieMiddleware $cookieMiddleware
    )
    {
        $this->createUser = $createUser;
        $request2JsonMiddlewares = [
            new InjectableMiddleware($request2JsonMiddleware),
        ];
        $json2HttpResponseMiddlewares = [
            new InjectableMiddleware($json2HttpResponseMiddleware,
                function($response) {
                    setcookie('message', $response->getBody()-> read(60));
                    return $response;
                }
            ),
        ];
        parent::__construct(
            $request2JsonMiddlewares, $json2HttpResponseMiddlewares
        );
    }


    protected function getRedirectView(ResponseInterface $response):string {
        var_dump($response);
        if ($response->getStatusCode() == 200) {
            return '/register';
        } 

        return '/register';
    }


    protected function execRequest($request) {
        $post = $request->getParsedBody();
        return $this->createUser->execute($post['json']);
    }

    protected function execResponse($response) {
        header('Location: '.$this->getRedirectView($response));
        exit;
    }
}