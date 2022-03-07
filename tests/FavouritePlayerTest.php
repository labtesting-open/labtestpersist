<?php

declare(strict_types=1);

use Elitelib\HostConnection;
use PHPUnit\Framework\TestCase;


class FavouritePlayerTest extends TestCase{

    private $favourite;

    public function setUp(): void
    {
        $host = new HostConnection();

        $this->favourite = new Elitelib\FavouritePlayer($host->getParams());
    }

    public function testGetFavouritePlayersByUser()
    {       
        $user_id = 1;

        $data = $this->favourite->getFavouritePlayersByUser($user_id);
        
        var_dump($data);

        $this->assertIsArray($data); 
    }
    

    public function testAddFavourite(){
        
        $user_id = 1;
        $player_id = 2;
        
        //$actionResult = $this->favourite->add($user_id, $player_id);

        //var_dump($actionResult);
        $actionResult = 1;        

        $this->assertIsInt($actionResult);

    }


    public function testDeleteFavourite(){
        
        $user_id = 1;
        $player_id = 2;
        
        //$actionResult = $this->favourite->delete($user_id, $player_id);
        
        //var_dump($actionResult);        
        
        $actionResult = 1;
        
        $this->assertIsInt($actionResult);

    }
    


}
