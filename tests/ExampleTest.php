<?php

declare(strict_types=1);

use Elitelib\HostConnection;
use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{

    private $op;

    public function setUp(): void
    {
        $host = new HostConnection();

        $this->op = new \Elitelib\Operations($host->getParams());
    }

    public function testAdd()
    {
        $result = $this->op->add(20, 5);
        $this->assertEquals(25, $result);
    }

    // public function testAddNotNumeric()
    // {
    //     $result = $this->op->add(null, null);
    //     $this->expectException(InvalidArgumentException::class);
    // }

    // public function testSubtract()
    // {
    //     $result = $this->op->subtract(20, 5);
    //     $this->assertEquals(15, $result);
    // }

    // public function testMultiply()
    // {
    //     $result = $this->op->multiply(20, 5);
    //     $this->assertEquals(100, $result);
    // }


    // public function testDiv()
    // {
    //     $result = $this->op->divide(20, 5);
    //     $this->assertEquals(4, $result);
    // }

    // public function testDivZero()
    // {
    //     $result = $this->op->divide(20, 0);
    //     $this->expectException(InvalidArgumentException::class);
    // }
}
