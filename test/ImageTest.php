<?php

require_once __DIR__ . '/../src/Image.php';
require_once __DIR__ . '/../src/FileManager.php';

use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{

    public function setUp(){
        $_SERVER['DOCUMENT_ROOT'] = "../";
    }

    public function testNormalImageShouldNotBeDummy(): void
    {
        // when
        $image = new Image("name", "path", IMAGETYPE_JPEG, 800, 600, 0, 0, new ExifData());

        // then
        $this->assertFalse($image->isDummy());
    }

    public function testDummyImageShouldBeDummy(): void
    {
        // given
        $fileManager = new FileManager();

        // when
        $textboxImage = Image::getTextboxFakeImage("textbox");
        $thumbnailCrationImage = Image::getThumbnailsGenerationDummyImage($fileManager);
        $noImagesImage = Image::getNoImagesDummyImage($fileManager);

        // then
        $this->assertTrue($textboxImage->isDummy());
        $this->assertTrue($thumbnailCrationImage->isDummy());
        $this->assertTrue($noImagesImage->isDummy());
    }

}
