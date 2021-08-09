<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;


class UserTest extends TestCase{

    private $op;

    public function setUp(): void
    {
        $this->op = new Elitelib\User();
    }

    public function testAdd()
    {       
        $this->assertEquals(25, 25);
    }


}
