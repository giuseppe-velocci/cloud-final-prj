<?php
declare(strict_types=1);

namespace App\Controller\Views;

use Psr\Http\Message\ServerRequestInterface;
use App\Controller\AbsController;
use App\Controller\ViewController;
use App\Middleware\InjectableMiddleware;
use App\Helper\ViewControllerDependencies;
use App\Middleware\Auth\NeedsAuthMiddleware;
use App\Db\ImagesDbCollection;
use App\Db\UserDbCollection;

class PhotoMaps extends ViewController implements \App\Controller\IController {
    use \App\Traits\GetCookieTrait;
    
    public function __construct(
        ViewControllerDependencies $view,
        NeedsAuthMiddleware $needsAuth,
        UserDbCollection $userCollection,
        ImagesDbCollection $imgCollection
    ) {
        $this->userCollection = $userCollection;
        $this->imgCollection  = $imgCollection;

        $template = 'photomaps';
        $middlewares = [
            new InjectableMiddleware($needsAuth)
        ];
        parent::__construct($template, $view, $middlewares);

    }

    /**
     * If some data is needed work here!
     */
    protected function setViewParams($request) :array{
        $cookies = $this->getCookies($request);

        $images=[];
        // here are selected only the images WITH exif data
        if ($this->userCollection->findByEmail($cookies['user'])) {
            $images = $this->imgCollection->selectAllByUser(
                $this->userCollection->mapObj->getId(),
                ['exif' => ['$ne' => '']],
                false
            );
        }

        return [
            'images' => $images
        ];
    }
}