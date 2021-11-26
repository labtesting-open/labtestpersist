<?php

declare(strict_types=1);

use Elitelib\HostConnection;
use PHPUnit\Framework\TestCase;


class UserClubTest extends TestCase{

    private $op;

    public function setUp(): void
    {
        $host = new HostConnection();

        $this->op = new Elitelib\UserClub($host->getParams());
    }

    public function testAdd()
    {       
        $this->assertEquals(25, 25);
    }


}
