<?php
declare(strict_types=1);

namespace App\Controller\Access;

use App\Controller\AbsController;

class Logout extends AbsController implements \App\Controller\IController {
    
    public function __construct() {
        $middlewares = [];
        parent::__construct($middlewares);
    }

    protected function controllerResponse($request) {
        $cookies = $request->getCookieParams();
        foreach ($cookies AS $name => $val) {
            setcookie($name, '', 1);
        }

        header('Location: /');
        exit;
    }
}