<?php

declare(strict_types=1);

use Elitelib\HostConnection;
use PHPUnit\Framework\TestCase;



class PlayerTest extends TestCase{

    private $player;

    public function setUp(): void
    {
        $host = new HostConnection();

        $this->player = new Elitelib\Player($host->getParams());
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
        $category_id = null;
        $division_id = null;
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


    public function testGetTeamPlayersInfoAndStaticsByPositionV2()
    {

        $club_id = 1; 
        $team_id = 1;
        $season_id = 2;          
        $position_id = 4;
        $language_code = null;
        $order = 'player_name';
        $order_sense = null;
        $find = 'carlos';  

        $players = $this->player->getTeamPlayersInfoAndStaticsByPositionV2(
            $club_id,
            $team_id,
            $season_id,          
            $position_id,
            $language_code,
            $order,
            $order_sense,
            $find 
        );

        //var_dump($players);

        $this->assertFalse(empty($players));

    }

    public function testGetPlayerPerfil()
    {
        $player_id = 1;
        $language_code = null;

        $player = $this->player->getPlayerPerfil($player_id, $language_code );

        //var_dump($player);

        $this->assertFalse(empty($player));
    }

    public function testSearchQuick()
    { 
        $find = null;
        $limit = null;
        $language_code = null;
        $order = null;
        $order_sense = null;

        $searchResult = $this->player->searchQuick(
            $find, 
            $limit, 
            $language_code, 
            $order, 
            $order_sense
        );

        //var_dump($searchResult);

        $this->assertFalse(empty($searchResult));

    }
    

}
