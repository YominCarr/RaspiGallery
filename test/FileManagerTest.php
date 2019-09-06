<?php

require_once __DIR__ . '/../src/FileManager.php';

use PHPUnit\Framework\TestCase;

class FileManagerTest extends TestCase
{

    /**
     * @dataProvider providePathsToConcat
     */
    public function testShouldConcatPaths($path1, $path2, $expectedConcatenatedPath)
    {
        // when
        $concat = (new FileManager())->concatPaths($path1, $path2);

        // then
        $this->assertEquals($expectedConcatenatedPath, $concat);
    }

    public function providePathsToConcat() {
        return array(
            array("asd", "yxc", "asd/yxc"),
            array("", "abc", "abc"),
            array("abc", "", "abc"),
            array("", "", ""),
            array("asd", "/yxc", "asd/yxc"),
            array("asd/", "yxc", "asd/yxc"),
            array("asd/", "/yxc", "asd/yxc"),
        );
    }
}
