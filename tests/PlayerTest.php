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
        
        var_dump($dataFilters);

        $this->assertFalse(empty($dataFilters)); 
      
    }



}
