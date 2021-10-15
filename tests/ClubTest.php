<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;


class ClubTest extends TestCase{

    private $club;

    public function setUp(): void
    {
        $this->club = new Elitelib\Club();
    }

    public function testGetAvailableClubs(){

        $continent_code='SA'; 
        $country_code = null; 
        $category_id = null;
        $division_id = null;
        $club_id = null;
        $nacionality_code = null;
        $orderField = null;
        $orderSense = null;

        $data_club = $this->club->getAvailableClubs(
            $continent_code, 
            $country_code, 
            $category_id,
            $division_id,
            $club_id,
            $nacionality_code,
            $orderField,
            $orderSense
        );
        
        var_dump($data_club);

        $this->assertFalse(empty($data_club)); 
      
    }


}
