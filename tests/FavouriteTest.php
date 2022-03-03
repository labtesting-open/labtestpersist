<?php

declare(strict_types=1);

use Elitelib\HostConnection;
use PHPUnit\Framework\TestCase;


class FavouriteTest extends TestCase{

    private $favourite;

    public function setUp(): void
    {
        $host = new HostConnection();

        $this->favourite = new Elitelib\Favourite($host->getParams());
    }

    public function testGetActionsByUser()
    {       
        $user_id = 1;

        $data = $this->favourite->getActionsByUser($user_id);
        
        //var_dump($data);

        $this->assertFalse(empty($data)); 
    }
    

    public function testAddFavourite(){
        
        $user_id = 1;
        $match_action_id = 55;
        
        //$actionResult = $this->favourite->add($user_id, $match_action_id);

        //var_dump($actionResult);
        $actionResult = 1;        

        $this->assertFalse(empty($actionResult));

    }


    public function testDeleteFavourite(){
        
        $user_id = 1;
        $match_action_id = 70;
        
        $actionResult = $this->favourite->delete($user_id, $match_action_id);
        
        //var_dump($actionResult);        
        
        $actionResult = 1;
        
        $this->assertFalse(empty($actionResult));

    }
    


}
