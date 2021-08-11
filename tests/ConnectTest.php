<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;


class ConnectTest extends TestCase{

    private $conn;

    protected function setUp(): void
    {
        $this->conn = new Elitelib\Connect();
    }

    public function testDatosDeConeccion()
    {   
        $data = $this->conn->getDataBase();  
        var_dump($data);  
        $this->assertFalse(empty($data));
    }


}
