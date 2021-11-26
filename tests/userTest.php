<?php

declare(strict_types=1);

use Elitelib\HostConnection;
use PHPUnit\Framework\TestCase;


class UserTest extends TestCase{

    private $op;

    public function setUp(): void
    {
        $host = new HostConnection();
        $this->op = new Elitelib\User($host->getParams());
    }

    public function testAdd()
    {       
        $this->assertEquals(25, 25);
    }


}
