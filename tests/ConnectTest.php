<?php

declare(strict_types=1);

use Elitelib\HostConnection;
use PHPUnit\Framework\TestCase;


class ConnectTest extends TestCase{

    private $conn;

    protected function setUp(): void
    {
        $host = new HostConnection();

        $this->conn = new Elitelib\Connect($host->getParams());
    }

    public function testDatosDeConeccion()
    {   
        $data = $this->conn->getDataBase();  
        var_dump($data);  
        $this->assertFalse(empty($data));
    }


}
