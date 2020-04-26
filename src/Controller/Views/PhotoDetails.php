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
    use \App\Traits\GetMessageTrait;

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
        $this->getResultMessage($request);

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

        if(! empty($imgDetails->shares)) {
            foreach ($imgDetails->shares AS $k => $v){
                $expires = substr($v, strpos($v, 'se=')+3, 10);
                if (strtotime($expires) - time() < 0) {
                    unset($imgDetails->shares[$k]);
                } else {
                    $imgDetails->shares->$k = $expires;
                }
            }
        }

        $port = ':' . $request->getUri()->getPort() ?? ''; 
        $sharePath = $request->getUri()->getScheme().'://'.$request->getUri()->getHost() . $port . '/shared/';
        
        return [
            'sharePath'  => $sharePath,
            'imgDetails' => $imgDetails,
            'message'  => $this->message,
            'msgStyle' => $this->msgStyle,
        ];
    }
}