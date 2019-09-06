<?php

use PHPUnit\Framework\TestCase;

class Test extends TestCase
{
    public function testCanBeUsedAsString(): void
    {
        $this->assertEquals(true, true);
        $this->assertEquals(true, false);
    }
}
