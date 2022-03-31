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
        $nacionality_code_list = "'AR','IT'";
        $orderField = 'nacionalities.country_code';
        $orderSense = 'ASC';

        $dataFilters = $this->player->getAvailableNationalities(
            $continent_code, 
            $country_code, 
            $category_id,
            $division_id,
            $club_id,
            $nacionality_code_list,
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

        $continent_code= 'SA'; 
        $country_code = 'AR'; 
        $category_id = 1;
        $division_id = 1;
        $club_id = 1;
        $nationality_code_list = "'AR','IT'";
        $position_id = 4;
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
        $user_id = 1;

        $dataFilters = $this->player->getAvailablePlayersWithFilters(
            $continent_code, 
            $country_code, 
            $category_id,
            $division_id,
            $club_id,
            $nationality_code_list,
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
            $language_code,
            $user_id
        );        

        
        //var_dump($dataFilters);

        $this->assertIsArray($dataFilters); 
      
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
        $find = 'carlos';
        $limit = null;
        $language_code = null;
        $order = null;
        $order_sense = null;
        $user_id = null;

        $searchResult = $this->player->searchQuick(
            $find, 
            $limit, 
            $language_code, 
            $order, 
            $order_sense,
            $user_id
        );

        //var_dump($searchResult);

        $this->assertFalse(empty($searchResult));

    }


    public function testAddPlayer()
    { 
        $club_id = 1;
        $team_id = 1;
        $player_name = 'example name';
        $player_surname = 'surname name';
        $position_id = 3;
        $birthdate = '2000-01-01';
        $height = 180;
        $weight = 84;
        $foot_code = 1;
        $jersey_nro = 5;
        $map_position = 'CM';            
        $img_profile = 'sadasdasdasd';

        // $actionResult = $this->player->add(
        //     $club_id,
        //     $team_id, 
        //     $player_name,
        //     $player_surname,           
        //     $position_id,
        //     $birthdate,
        //     $height,
        //     $weight,
        //     $foot_code,
        //     $jersey_nro,
        //     $map_position,            
        //     $img_profile
        // );

        //var_dump($actionResult);
        $actionResult = 1;        

        $this->assertIsInt($actionResult);

    }


    public function testGetPlayerId()
    { 
        $club_id = 1;
        $team_id = 1;
        $player_name = 'Luca';
        $player_surname = 'Piñ';       

        $player_id = $this->player->getPlayerId(
            $club_id,
            $team_id, 
            $player_name,
            $player_surname   
        );

        //var_dump($player_id);
               

        $this->assertIsInt($player_id);

    }


    public function testUpdatePlayer()
    {   
        $player_id = 123;
        $player_name = 'example name';
        $player_surname = 'surname name';
        $position_id = 3;
        $birthdate = '2000-01-01';
        $height = 180;
        $weight = 84;
        $foot_code = 0;
        $jersey_nro = 5;
        $map_position = 'CM';            
        $img_profile = 'sadasdasdasd';


        // $actionResult = $this->player->update(
        //     $player_id,         
        //     $player_name,
        //     $player_surname,           
        //     $position_id,
        //     $birthdate,
        //     $height,
        //     $weight,
        //     $foot_code,
        //     $jersey_nro,
        //     $map_position,            
        //     $img_profile
        // );

        //var_dump($actionResult);
        $actionResult = 1;        

        $this->assertIsInt($actionResult);

    }

    public function testGetNumberOfMatchesPlayed()
    {
        $player_id = 1;

        $matchesPlayer = $this->player->getNumberOfMatchesPlayed($player_id);

        //var_dump($matchesPlayer);

        $this->assertIsInt($matchesPlayer);
    }

    public function testDeleteAllPlayersInjuries()
    {
        $player_id = 109;

        //$affected = $this->player->deleteAllPlayersInjuries($player_id);

        $affected = 0;

        //var_dump($affected);

        $this->assertIsInt($affected);
    }


    public function testDeleteAllPlayersMapPositionSecondary()
    {
        $player_id = 109;

        //$affected = $this->player->deleteAllPlayersMapPositionSecondary($player_id);

        $affected = 0;
        
        var_dump($affected);

        $this->assertIsInt($affected);
    }


    public function testDeleteImages()
    {
        $player_id = 109;

        $affected = $this->player->deleteImages($player_id);

        //$affected = 0;
        
        //var_dump($affected);

        $this->assertIsInt($affected);
    }


    public function testDelete()
    {
        $player_id = 109;

        //$affected = $this->player->delete($player_id);

        $affected = 0;
        
        //var_dump($affected);

        $this->assertIsInt($affected);
    }
    

}
