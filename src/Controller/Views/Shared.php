<?php
declare(strict_types=1);

namespace App\Controller\Views;

use Psr\Http\Message\ServerRequestInterface;
use App\Controller\AbsController;
use App\Controller\ViewController;
use App\Helper\ViewControllerDependencies;
use App\Middleware\InjectableMiddleware;
use App\Db\ImagesDbCollection;


class Shared extends ViewController implements \App\Controller\IController {

    public function __construct(
        ViewControllerDependencies $view,
        ImagesDbCollection $imagesDb
    ) {
        $this->imagesDb = $imagesDb;

        $template = 'shared';
        $middlewares = [
            
        ];
        parent::__construct($template, $view, $middlewares);
    }

    protected function getImageDetailsFromDb (string $guid) :array {
        return $this->imagesDb->select(['shares.'.$guid => ['$exists' => true]])->toArray();
    }

    protected function setViewParams($request) :array{
        $splitPath = explode('/', $request->getUri()->getPath());
        if (count($splitPath) < 2) {
            throw new \Exception('Invalid request.', 400);
        }
        $guid = $splitPath[2];

        //select image from db to get data
        $imgDetails = $this->getImageDetailsFromDb($guid)[0];

        if (is_null($imgDetails)) {
            throw new \Exception('Not Found.', 404);
        }

        return [
            'imgDetails' => $imgDetails,
            'guid' => $guid
        ];
    }
}