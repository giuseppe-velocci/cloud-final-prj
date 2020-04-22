<?php
declare(strict_types=1);

namespace App\Controller\Views;

use Psr\Http\Message\ServerRequestInterface;
use App\Controller\AbsController;
use App\Controller\ViewController;
use App\Helper\ViewControllerDependencies;
use App\Middleware\InjectableMiddleware;
use App\Middleware\Auth\NeedsAuthMiddleware;
use App\Db\ImagesDbCollection;
use App\Db\UserDbCollection;

class PhotoManager extends ViewController implements \App\Controller\IController {
    use \App\Traits\GetMessageTrait;
    use \App\Traits\GetCookieTrait;

    public function __construct(
        ViewControllerDependencies $view,
        NeedsAuthMiddleware $needsAuth,
        UserDbCollection $userCollection,
        ImagesDbCollection $imgCollection
    ) {
        $this->userCollection = $userCollection;
        $this->imgCollection  = $imgCollection;
        $template = 'photomanager';
        $middlewares = [
            new InjectableMiddleware($needsAuth)
        ];
        parent::__construct($template, $view, $middlewares);
    }

    
    protected function setViewParams($request) :array{
        $cookies = $this->getCookies($request);

        $images = [];
        if($this->userCollection->findByEmail($cookies['user'])) {
            $images = $this->imgCollection->selectAllByUser(
                $this->userCollection->mapObj->getId(),
                $this->isLastLoginNearExpiry($cookies)
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