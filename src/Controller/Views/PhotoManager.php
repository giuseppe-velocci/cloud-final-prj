<?php
declare(strict_types=1);

namespace App\Controller\Views;

use Psr\Http\Message\ServerRequestInterface;
use App\Controller\AbsController;
use App\Controller\ViewController;
use App\Helper\ViewControllerDependencies;
use App\Helper\CurlApiHelper;
use App\Middleware\InjectableMiddleware;
use App\Middleware\Auth\NeedsAuthMiddleware;
use App\Db\ImagesDbCollection;
use App\Db\UserDbCollection;
use App\Controller\Actions\GetImageAction;

class PhotoManager extends ViewController implements \App\Controller\IController {
    use \App\Traits\GetMessageTrait;
    use \App\Traits\GetCookieTrait;

    public function __construct(
        ViewControllerDependencies $view,
        NeedsAuthMiddleware $needsAuth,
        UserDbCollection $userCollection,
        ImagesDbCollection $imgCollection

    //    GetImageAction $getImg
    ) {
        $this->userCollection = $userCollection;
        $this->imgCollection  = $imgCollection;
   //     $this->getImg = $getImg;

        $template = 'photomanager';
        $middlewares = [
            new InjectableMiddleware($needsAuth)
        ];
        parent::__construct($template, $view, $middlewares);
    }

    
    protected function setViewParams($request) :array{
        $cookies = $this->getCookies($request);
/*
        $port = ':' . $request->getUri()->getPort() ?? ''; 
        $endpoint = $request->getUri()->getHost() . $port . '/getimage';

        $images = [];
        $queryString = [
            'json' => json_encode(
                ['user' => $cookies['user']]
            )
        ];
    var_dump(CurlApiHelper::get($endpoint, $queryString, $cookies['token']));
    // var_dump($this->getImg->execute($request));
         exit();
    */    
        if ($this->userCollection->findByEmail($cookies['user'])) {
            $images = $this->imgCollection->selectAllByUser(
                $this->userCollection->mapObj->getId()
            );
        }
        


        $this->getResultMessage($request);
        return [
            'message' => $this->message,
            'msgStyle' => $this->msgStyle,
            'images' => $images
        ];
    }
}