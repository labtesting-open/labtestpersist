<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;


class PlayerTest extends TestCase{

    private $player;

    public function setUp(): void
    {
        $this->player = new Elitelib\Player();
    }

    public function testGetAvailableNationalities(){

        $continent_code='SA'; 
        $country_code = null; 
        $category_id = null;
        $division_id = null;
        $club_id = null;
        $nacionality_code = NULL;
        $orderField = 'nacionalities.country_code';
        $orderSense = 'ASC';

        $dataFilters = $this->player->getAvailableNationalities(
            $continent_code, 
            $country_code, 
            $category_id,
            $division_id,
            $club_id,
            $nacionality_code,
            $orderField,
            $orderSense
        );
        
        //var_dump($dataFilters);

        $this->assertFalse(empty($dataFilters)); 
      
    }

    public function testGetAvailablePlayers(){

        $continent_code=null; 
        $country_code = 'AR'; 
        $category_id = null;
        $division_id = null;
        $club_id = 1;
        $nacionality_code = NULL;
        $position_id =1;
        $orderField = null;
        $orderSense = null;

        $dataFilters = $this->player->getAvailablePlayers(
            $continent_code, 
            $country_code, 
            $category_id,
            $division_id,
            $club_id,
            $nacionality_code,
            $position_id,
            $orderField,
            $orderSense
        );
        
        //var_dump($dataFilters);

        $this->assertFalse(empty($dataFilters)); 
      
    }

    public function testGetAllPositions(){
        
        $position_id = null;
        $orderField = null;
        $orderSense = null;
        $language_code = 'ES';

        $dataFilters = $this->player->getAllPositions(            
            $position_id,
            $orderField,
            $orderSense,
            $language_code
        );
        
        var_dump($dataFilters);

        $this->assertFalse(empty($dataFilters)); 
      
    }



}
