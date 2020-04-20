<?php
declare(strict_types=1);

namespace App\Db;

use App\Helper\Validator;
use MongoDB\BSON\ObjectId;

class Images extends BaseMapObject{ 
    protected $_id;
    protected $url;
    protected $filename;
    protected $userId;
    protected $tags;
    protected $exif;

    protected $required = ['url', 'userId'];
    protected $dataTypes = [
		'_id'       => Validator::MONGOID,
        'url'       => Validator::URL,
        'filename'  => Validator::FILENAME,
		'userId'    => Validator::MONGOID,
		'tags'      => Validator::NAME, 
		'exif'      => Validator::TEXT
	];

    public function __construct(
        ObjectId $id = null, 
        string $url = '', 
        string $filename = '',
        ObjectId $userId = null, 
        array  $tags = [], 
        ?array $exif = null
    ){
		$this->setId($id);
        $this->setUrl($url);
        $this->setFilename($filename);
        $this->setUserId($userId);
        $this->setTags($tags);

        if (! is_null($exif))
            $this->setExif($exif);
	}

    public function getId() {
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

    public function getFilename() {
        return $this->filename;
    }
    public function setFilename($filename) {
        $this->filename = $filename;
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