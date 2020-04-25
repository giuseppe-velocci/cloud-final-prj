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
    protected $shares; // guid param for sas url: "urlGuid" => "sasUrl"

    protected $required = ['url', 'userId'];
    protected $dataTypes = [
		'_id'       => Validator::MONGOID,
        'url'       => Validator::URL,
        'filename'  => Validator::FILENAME,
		'userId'    => Validator::MONGOID,
		'tags'      => Validator::NAME, 
        'exif'      => Validator::TEXT,
        'shares'    => Validator::TEXT
	];

    public function __construct(
        ObjectId $id = null, 
        string $url  = '', 
        string $filename = '',
        ObjectId $userId = null, 
        array $tags = [], 
        array $exif = [],
        array $shares = []
    ){
		$this->setId($id);
        $this->setUrl($url);
        $this->setFilename($filename);
        $this->setUserId($userId);
        $this->setTags($tags);
        $this->setExif($exif);
        $this->setShares($shares);
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


    public function getShares($shares) {
        return $this->shares;
    }
    public function setShares($shares) {
        $this->shares = $shares;
    }
}