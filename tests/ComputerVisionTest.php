<?php
declare(strict_types=1);

use PHPUnit\Framework\TestCase;
use App\Img\ComputerVision;

final class ComputerVisionTest extends TestCase
{
    protected $cv;
    protected $imgUrl = 'https://it.wikipedia.org/wiki/Monte_Bianco#/media/File:Mont_Blanc,_Mont_Maudit,_Mont_Blanc_du_Tacul.jpg';
//    protected $imgPath = './uploads/imgt2.jpg';


    public function setUp(): void {
 //       chdir(dirname(__DIR__));
        $this->cv = new ComputerVision();
/*
        if (! file_exists($this->imgPath)) { 
            $im = @imagecreatetruecolor(20, 20) or die('Cannot Initialize new GD image stream');
            imagejpeg($im, $this->imgPath);
        }
    */
   }
/*
    public function tearDown() :void {
        if (file_exists($this->imgPath))
            unlink($this->imgPath);
    }
*/

    public function testReturnValueIsJson() : void
    {
        json_decode($this->cv->analyze($this->imgUrl));
        $this->assertEquals(json_last_error(), JSON_ERROR_NONE);
    }
}