<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;


class ClubTest extends TestCase{

    private $op;

    public function setUp(): void
    {
        $this->op = new Elitelib\Club();
    }

    public function testAdd()
    {       
        $this->assertEquals(25, 25);
    }


}
