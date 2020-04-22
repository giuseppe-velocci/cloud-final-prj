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


class PhotoDetails extends ViewController implements \App\Controller\IController {
    
    public function __construct(
        ViewControllerDependencies $view,
        NeedsAuthMiddleware $needsAuth,
        ImagesDbCollection $imagesDb
    ) {
        $this->imagesDb = $imagesDb;

        $template = 'photodetails';
        $middlewares = [
            new InjectableMiddleware($needsAuth)
        ];
        parent::__construct($template, $view, $middlewares);
    }

    protected function getImageDetailsFromDb (string $imgPath) :array {
        return $this->imagesDb->select(['filename' => $imgPath])->toArray();
    }

    protected function setViewParams($request) :array{
        $splitPath = explode('/', $request->getUri()->getPath());
        if (count($splitPath) < 2) {
            throw new \Exception('Invalid request.', 400);
        }
        $imgPath = str_replace('%20', '.', $splitPath[2]);

        //select image from db to get data
        $imgDetails = $this->getImageDetailsFromDb ($imgPath)[0];

        if (is_null($imgDetails)) {
            throw new \Exception('Not Found.', 404);
        }

        return [
            'imgDetails' => $imgDetails
        ];
    }
}