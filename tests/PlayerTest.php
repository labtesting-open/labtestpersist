<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;


class PlayerTest extends TestCase{

    private $op;

    public function setUp(): void
    {
        $this->op = new Elitelib\Player();
    }

    public function testAdd()
    {       
        $this->assertEquals(25, 25);
    }


}
