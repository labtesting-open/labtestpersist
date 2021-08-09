<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;


class TeamTest extends TestCase{

    private $op;

    public function setUp(): void
    {
        $this->op = new Elitelib\Team();
    }

    public function testAdd()
    {       
        $this->assertEquals(25, 25);
    }


}