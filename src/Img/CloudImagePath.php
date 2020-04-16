<?php
declare(strict_types=1);

namespace App\Img;

use App\Config\Env;

class CloudImagePath {
    protected $basePath;

    public function __construct() {
        try {
            $account = Env::get('AZURE_BLOB_ACCOUNT');
            $container = Env::get('AZURE_CONTAINER');

            $this->basePath = "https://$account.blob.core.windows.net/$container/";
        } catch (\InvalidArgumentException $e) {
            die($e->getMessage());
        }        
    }

    public function getFullPath(string $imgPath) :string {
        return $this->basePath . $imgPath;
    }
}
