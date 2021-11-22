<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;


class TeamTest extends TestCase{

    private $team;

    public function setUp(): void
    {
        $this->team = new Elitelib\Team();
    }

    public function testGetAvailableTeamsWithFilters(){

        $continent_code = 'SA';
        $country_code = null; 
        $category_id = null;
        $division_id = null;           
        $order_field = null;
        $order_sense = null;
        $page = 1;
        $limit = 100;
        $language_code = null;

       $data = $this->team->getAvailableTeamsWithFilters(
        $continent_code, 
        $country_code, 
        $category_id,
        $division_id,                
        $order_field,
        $order_sense,
        $page,
        $limit,
        $language_code
       );
        
        //var_dump($data);

        $this->assertFalse(empty($data)); 
      
    }

    public function testGetAvailableTeamsWithFiltersTotalRows(){

        $continent_code = 'SA';
        $country_code = null; 
        $category_id = null;
        $division_id = null;           
        $order_field = null;
        $order_sense = null;
        $page = 1;
        $limit = 100;
        $language_code = null;

       $pagesRows = $this->team->getAvailableTeamsWithFiltersTotalRows(
        $continent_code, 
        $country_code, 
        $category_id,
        $division_id,                
        $order_field,
        $order_sense,
        $page,
        $limit,
        $language_code
       );
        
      // $totalPages = ceil($pagesRows / $limit);

      // echo "total rows: $pagesRows - total pages: $totalPages";

       $this->assertFalse(empty($pagesRows)); 
      
    }

    public function testAddteam(){
        
        $club_id = 1;
        $category_id = 2;
        $division_id = 4;
        $team_name = 'nuevo team';
        $img_team = 'pepelepu';
        
        $actionResult = $this->team->add($club_id, 
        $category_id, 
        $division_id, 
        $team_name, 
        $img_team
        );

        var_dump($actionResult);
        
        $this->assertFalse(empty($actionResult));

    }


}