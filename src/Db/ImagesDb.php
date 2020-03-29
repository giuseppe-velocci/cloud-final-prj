<?php
declare(strict_types=1);

namespace App\Db;

class ImagesDb extends GenericDbCollection{
    
    protected $id;
    protected $url;
    protected $userId;
    protected $tags;
    protected $exif;


    public function __construct(string $id, string $url, string $userId, array $tags, ?array $exif=null) {
        $this->setId($id);
        $this->setUrl($url);
        $this->setUserId($userId);
        $this->setTags($tags);
        $this->setExif($exif);
    }

    public function getId(): string {
        return $this->id ;
    }
    public function setId($id) {
        $this->id = $id;
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