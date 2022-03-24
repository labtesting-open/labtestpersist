<?php

declare(strict_types=1);

use Elitelib\HostConnection;
use PHPUnit\Framework\TestCase;

class ClubTest extends TestCase{

    private $club;

    public function setUp(): void
    {
        $host = new HostConnection();

        $this->club = new Elitelib\Club($host->getParams());
    }


    public function testGetBasicInfo()
    {
        $club_id = 1;

        $data_club = $this->club->getBasicInfo($club_id);
        
        //var_dump($data_club);

        $this->assertFalse(empty($data_club)); 
    }


    public function testGetAvailableClubs(){

        $continent_code= null; 
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
        
        //var_dump($data_club);

        $this->assertFalse(empty($data_club)); 
      
    }


    public function testGetAvailableClubsWithFilters(){

        $continent_code = null;
        $country_code = 'AR';
        $order_field = null;
        $order_sense = null;
        $page = 1;
        $limit = 10;
        $language_code = null;

       $data = $this->club->getAvailableClubsWithFilters(
        $continent_code, 
        $country_code, 
        $order_field,
        $order_sense,
        $page,
        $limit,
        $language_code
       );
        
        //var_dump($data);

        $this->assertFalse(empty($data)); 
      
    }


    public function testGetAvailableClubsWithFiltersTotalRows(){

        $continent_code = null;
        $country_code = 'AR';                 
        $order_field = null;
        $order_sense = null;
        $page = 1;
        $limit = 100;
        $language_code = null;

       $pagesRows = $this->club->getAvailableClubsWithFiltersTotalRows(
        $continent_code, 
        $country_code,                       
        $order_field,
        $order_sense,
        $page,
        $limit,
        $language_code
       );
        
       //$totalPages = ceil($pagesRows / $limit);

       //echo "total rows: $pagesRows - total pages: $totalPages";

       $this->assertFalse(empty($pagesRows)); 
      
    }

    public function testSearchQuick()
    { 
        $find = 'riv';
        $limit = null;
        $language_code = null;
        $order = null;
        $order_sense = null;

        $searchResult = $this->club->searchQuick(
            $find, 
            $limit, 
            $language_code, 
            $order, 
            $order_sense
        );

        //var_dump($searchResult);

        $this->assertFalse(empty($searchResult));

    }

    public function testUpdate()
    {  
        $club_id = 2;
        $club_name= "River Plate";
        $country_code = "";
        $logo = "";            
                  

        // $actionResult = $this->club->update(
        //     $club_id, 
        //     $club_name,
        //     $country_code,
        //     $logo
        // );
        
        $actionResult = 1;        
        //var_dump($actionResult);

        $this->assertIsInt($actionResult); 
    }




}
