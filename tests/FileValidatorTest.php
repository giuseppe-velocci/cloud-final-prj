<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Helper\FileValidator;

final class FileValidatorTest extends TestCase
{
    protected static $class;

    protected static $imgPath = '/uploads/imgtst.jpg';
    protected static $wrongFile = '/uploads/wrong.txt';


    public static function setUpBeforeClass(): void {
        self::$class = new FileValidator();

        self::$wrongFile = dirname(__DIR__) . self::$wrongFile;
        self::$imgPath = dirname(__DIR__) . self::$imgPath;
        
        if (! file_exists(self::$imgPath)) { 
            $im = @imagecreatetruecolor(20, 20) or die('Cannot Initialize new GD image stream');
            imagejpeg($im, self::$imgPath);
        }

        if (! file_exists(self::$wrongFile))
            touch(self::$wrongFile);
    }


    public static function tearDownAfterClass(): void {
        if (file_exists(self::$imgPath))
            unlink(self::$imgPath);
        
        if (file_exists(self::$wrongFile))
            unlink(self::$wrongFile);
      }

    /**
     * validateFilename()
     */
    public function testExceptionNoFilename(): void {
        $this->expectExceptionMessage('No given name for file.');

        self::$class->validateFilename('');
    }

    public function testExceptionFilenameTooLong(): void {
        $this->expectExceptionMessage('Name too long for file.');

        $name = str_repeat('a', self::$class::MAX_FILENAME_LEN+1);
        self::$class->validateFilename($name);
    }

    public function testExceptionInvalidChars(): void {
        $this->expectExceptionMessage('Invalid characters in file name.');

        $name = '*';
        self::$class->validateFilename($name);
    }

    /**
     * validateFile()
     */
    public function testExceptionInvalidFileType(): void {
        $this->expectExceptionMessage('Invalid file.');

        $name = self::$wrongFile;
        self::$class->validateFile($name);
    }


    public function testExceptionValidFileType(): void {
        $result = true;
        try {
            $name = self::$imgPath;
            self::$class->validateFile($name); 
        } catch (\InvalidArgumentException $e) {
            $result = false;
        }

        $this->assertTrue($result);
    }
}