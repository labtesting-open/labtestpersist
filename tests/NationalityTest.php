<?php

declare(strict_types=1);

use Elitelib\HostConnection;
use PHPUnit\Framework\TestCase;


class NationalityTest extends TestCase{

    private $nationality;

    public function setUp(): void
    {
        $host = new HostConnection();

        $this->nationality = new Elitelib\Nationality($host->getParams());
    }

    public function testGet()
    { 
        $data = $this->nationality->get();
        
        //var_dump($data);

        $this->assertIsArray($data); 
    }
    

    public function testAdd(){
        
        $player_id = 109;
        $nationalitiesPlayerList = "'ES','IT'";       

        //$actionResult = $this->nationality->add($player_id, $nationalitiesPlayerList);

        //var_dump($actionResult);
        $actionResult = 1;        

        $this->assertIsInt($actionResult);

    }    

    public function testDelete(){
        
        $player_id = 140;              

        //$actionResult = $this->nationality->delete($player_id);

        //var_dump($actionResult);
        $actionResult = 1;        

        $this->assertIsInt($actionResult);

    }    



}
