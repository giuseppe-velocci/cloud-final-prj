<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Img\Upload;
use App\Img\UploadException;

// gd extension needed for this tests (in cli)
final class UploadTest extends TestCase
{
    protected static $tmpUserFolder = '/users/usrtest';
    protected static $imgPath = '/users/imgtst.jpg';
    protected static $imgUploadedPath = '/users/usrtest/imgtst.jpg';
    protected static $wrongFile = '/users/usrtest/wrong.txt';
    
    protected static $upd;


    public static function setUpBeforeClass(): void
    {
        self::$tmpUserFolder = dirname(__DIR__) . self::$tmpUserFolder;
        self::$imgUploadedPath = dirname(__DIR__) . self::$imgUploadedPath ;
        self::$wrongFile = dirname(__DIR__) . self::$wrongFile;
        self::$imgPath = dirname(__DIR__) . self::$imgPath;
        
        $_FILES[Upload::UPLOAD_INPUT_NAME]['name'] = self::$imgPath;

        (file_exists(self::$tmpUserFolder) ? : mkdir(self::$tmpUserFolder, 0755));

        if (! file_exists(self::$imgPath)) { 
            $im = @imagecreatetruecolor(20, 20) or die('Cannot Initialize new GD image stream');
            imagejpeg($im, self::$imgPath);
        }

        if (! file_exists(self::$wrongFile))
            touch(self::$wrongFile);

        self::$upd = new Upload(self::$tmpUserFolder);
    }


    public static function tearDownAfterClass(): void
    {
        if (file_exists(self::$imgPath)){
            unlink(self::$imgPath);
        } elseif (file_exists(self::$tmpUserFolder. '/' . basename(self::$imgPath))) {
            unlink(self::$imgPath);
        }

        if (file_exists(self::$wrongFile))
            unlink(self::$wrongFile);

        rmdir(self::$tmpUserFolder);
      }


    // Tests

    public function testMissingErrorFieldInFILES(): void
    {
        self::expectExceptionCode(UploadException::EMPTY_FILE);
        self::$upd->uploadImg();
    }


    public function testDefaultUploadErrorThrowing() : void {
        $_FILES[Upload::UPLOAD_INPUT_NAME]['error'] = UploadException::INVALID_FILE;
        self::expectExceptionCode(UploadException::INVALID_FILE);
        self::$upd->uploadImg();
    }

    // not working..
    public function testInvalidFilenameUploadErrorThrowing() : void {
        $_FILES[Upload::UPLOAD_INPUT_NAME]['tmp_name'] = 
            str_repeat('a', App\Img\ImgValidator::MAX_FILENAME_LEN +1) ;
        $_FILES[Upload::UPLOAD_INPUT_NAME]['error'] = UPLOAD_ERR_OK;
  
        self::expectExceptionCode(UploadException::INVALID_FILENAME);
        self::$upd->uploadImg(); 
    }
    
    // not working... it did earlier...
    public function testWrongImgTypeUploadErrorThrowing() : void {
        $_FILES[Upload::UPLOAD_INPUT_NAME]['tmp_name'] = self::$wrongFile;
        $_FILES[Upload::UPLOAD_INPUT_NAME]['error'] = UPLOAD_ERR_OK;
        
        self::expectExceptionCode(UploadException::INVALID_FILE);
        self::$upd->uploadImg(); 
    }

    /*
    /**
     * @depends testWrongImgTypeUploadErrorThrowing
     
    public function testUploadSuccessful() {
        $_FILES[Upload::UPLOAD_INPUT_NAME]['tmp_name'] = self::$imgPath;
        $_FILES[Upload::UPLOAD_INPUT_NAME]['name'] = self::$imgUploadedPath;

        self::$upd->uploadImg();
    }
    */
}
