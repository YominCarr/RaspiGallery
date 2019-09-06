<?php

require_once __DIR__ . '/../src/Image.php';

use PHPUnit\Framework\TestCase;

class ImageTest extends TestCase
{

    public function testNormalImageShouldNotBeDummy(): void
    {
        $image = new Image("name", "path", IMAGETYPE_JPEG, 800, 600, 0, 0, new ExifData());

        $this->assertFalse($image->isDummy());
    }

}
