<?php
declare(strict_types=1);

namespace App\Db;

use App\Db\MongoWQuery;
use App\Helper\ISanitizer;
use App\Config\Env;

class Images extends BaseMapObject{ 
    protected $_id;
    protected $url;
    protected $userId;
    protected $tags;
    protected $exif;

    protected $required = ['url', 'userId', 'tags'];


    public function __construct(
        $id = null, 
        string $url = '', 
        string $userId = '', 
        array $tags = [], 
        ?array $exif = null
    ){
		$this->setId($id);
        $this->setUrl($url);
        $this->setUserId($userId);
        $this->setTags($tags);

        if (! is_null($exif))
            $this->setExif($exif);
	}

    public function getId(): string {
        return $this->_id;
    }
    public function setId($id) {
        $this->_id = $id;
    }

    public function getUrl() {
        return $this->url;
    }
    public function setUrl($url) {
        $this->url = $url;
    }

    public function getUserId() {
        return $this->userId;
    }
    public function setUserId($userId) {
        $this->userId = $userId;
    }

    public function getTags() {
        return $this->tags;
    }
    public function setTags($tags) {
        $this->tags = $tags;
    }

    public function getExif($exif) {
        return $this->exif;
    }
    public function setExif($exif) {
        $this->exif = $exif;
    }
}