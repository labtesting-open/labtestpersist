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


    public function testgetAllPrimaryPositions(){        
        
        $orderField = null;
        $orderSense = null;
        $language_code = null;

        $dataFilters = $this->player->getAllPrimaryPositions( 
            $orderField,
            $orderSense,
            $language_code
        );
        
        //var_dump($dataFilters);

        $this->assertFalse(empty($dataFilters)); 
      
    }

    public function testgetAllSecondaryPositions(){        
        
        $orderField = null;
        $orderSense = null;
        $language_code = null;

        $dataFilters = $this->player->getAllSecondaryPositions( 
            $orderField,
            $orderSense,
            $language_code
        );
        
        //var_dump($dataFilters);

        $this->assertFalse(empty($dataFilters)); 
      
    }

    public function testgetAvailablePlayersWithFilters(){

        $continent_code= null; 
        $country_code = 'AR'; 
        $category_id = 1;
        $division_id = 1;
        $club_id = 1;
        $nationality_code = null;
        $position_id = null;
        $second_positions_codes = null;
        $age_range = null;
        $height_range = null;
        $weight_range = null;
        $foot = null;
        $orderField = null;
        $orderSense = null;
        $page = 1;
        $limit = 100;
        $language_code = 'ES';

        $dataFilters = $this->player->getAvailablePlayersWithFilters(
            $continent_code, 
            $country_code, 
            $category_id,
            $division_id,
            $club_id,
            $nationality_code,
            $position_id,
            $second_positions_codes,
            $age_range,
            $height_range,
            $weight_range,
            $foot,
            $orderField,
            $orderSense,
            $page,
            $limit,
            $language_code
        );        

        
        //var_dump($dataFilters);

        $this->assertFalse(empty($dataFilters)); 
      
    }


    public function testgetAvailablePlayersWithFiltersPages(){

        $continent_code= null; 
        $country_code = 'AR'; 
        $category_id = 1;
        $division_id = 1;
        $club_id = 1;
        $nationality_code = null;
        $position_id = null;
        $second_positions_codes = null;
        $age_range = null;
        $height_range = null;
        $weight_range = null;
        $foot = null;
        $orderField = null;
        $orderSense = null;
        $page = 1;
        $limit = 100;
        $language_code = 'ES';

        $pagesRows = $this->player->getAvailablePlayersWithFiltersTotalRows(
            $continent_code, 
            $country_code, 
            $category_id,
            $division_id,
            $club_id,
            $nationality_code,
            $position_id,
            $second_positions_codes,
            $age_range,
            $height_range,
            $weight_range,
            $foot,
            $orderField,
            $orderSense,
            $page,
            $limit,
            $language_code
        );        

        
        //var_dump($pagesRows);

        $totalPages = ceil($pagesRows / $limit);

        //echo "total rows: $pagesRows - total pages: $totalPages";

        $this->assertFalse(empty($pagesRows)); 
      
    }


    public function testGetPlayersRangesOfMeasures(){      

        $dataFilters = $this->player->getPlayersRangesOfMeasures();
        
        //var_dump($dataFilters);

        $this->assertFalse(empty($dataFilters)); 
      
    }

}
