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

class Dashboard extends ViewController implements \App\Controller\IController {
    use \App\Traits\GetCookieTrait;

    public function __construct(
        ViewControllerDependencies $view,
        NeedsAuthMiddleware $needsAuth,
        ImagesDbCollection $imagesDb,
        UserDbCollection $userDb
    ) {
        $this->imagesDb = $imagesDb;
        $this->userDb = $userDb;

        $template = 'dashboard';
        $middlewares = [
            new InjectableMiddleware($needsAuth)
        ];
        parent::__construct($template, $view, $middlewares);
    }

    /**
     * Setup query params for mongoDb find
     */
    protected function setMongoSearch(array $queryParams) :array {
        $search = [];
        if (isset($queryParams['tag']) && ! empty($queryParams['tag'])) {
            $regex = new \MongoDB\BSON\Regex (str_replace(' ', '(_|\W)', trim($queryParams['tag'])), 'gi');
            $search ['tags'] = $regex;
        }

        if (isset($queryParams['exif']) && ! empty($queryParams['exif'])) {
            $regex = new \MongoDB\BSON\Regex (str_replace([' ', ':', ','], '(_|\W)+', trim($queryParams['exif'])), 'gi');
            $search ['exif'] = $regex;
        }
        return $search;
    }

    protected function setViewParams($request) :array{
        $cookies = $this->getCookies($request);
        if (! $this->userDb->findByEmail($cookies['user'])) {
            throw new \Exception('Invalid request.', 400);
        }

        $search = $this->setMongoSearch($request->getQueryParams());

        $images = $this->imagesDb->selectAllByUser(
            $this->userDb->mapObj->getId(), 
            $search
        );

        return [
            'images' => $images
        ];
    }
}