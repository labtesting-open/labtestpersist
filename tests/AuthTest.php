<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;


class AuthTest extends TestCase{

    private $op;

    public function setUp(): void
    {
        $this->op = new Elitelib\Auth();
    }

    public function testAdd()
    {       
        $this->assertEquals(25, 25);
    }


}
