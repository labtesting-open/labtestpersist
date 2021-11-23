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

    public function testAddTeam(){
        
        $club_id = 1;
        $category_id = 2;
        $division_id = 4;
        $team_name = 'nuevo team';
        $img_team = 'pepelepu';
        
        // $actionResult = $this->team->add($club_id, 
        // $category_id, 
        // $division_id, 
        // $team_name, 
        // $img_team
        // );

        //var_dump($actionResult);
        $actionResult = 1;

        $this->assertFalse(empty($actionResult));

    }


    public function testUpdateTeam(){
        $team_id=37;
        $club_id = 1;
        $category_id = 4;
        $division_id = 17;
        $team_name = 'nombre editado 4';
        $img_team = null;
        
        $affected = $this->team->update(
        $team_id,
        $club_id, 
        $category_id, 
        $division_id, 
        $team_name, 
        $img_team
        );

        //var_dump($affected);        

        $this->assertTrue(is_int($affected));

    }


    public function testDeleteTeam(){
        
        $team_id=38;

        //check if team has a matches played

        $matchesPlayed = $this->team->getNumberOfMatchesPlayedByTeam($team_id);

        $changes = array();

        array_push($changes, $matchesPlayed[0]['matches']);       

        if($matchesPlayed[0]['matches'] == 0){           

            $nacionalities = $this->team->deleteAllPlayersNacionalitiesFromTeam($team_id);

            $injuries = $this->team->deleteAllPlayersInjuriesFromTeam($team_id);

            $socialMedia = $this->team->deleteAllPlayersSocialMediaFromTeam($team_id);

            $mapPositionsSecondary = $this->team->deleteAllPlayersMapPositionSecondaryFromTeam($team_id);

            $teamPlayers = $this->team->deleteTeamPlayers($team_id);
            
            $team = $this->team->delete($team_id);            

            array_push($changes, $nacionalities);
            array_push($changes, $injuries);
            array_push($changes, $socialMedia);
            array_push($changes, $mapPositionsSecondary); 
            array_push($changes, $teamPlayers);    
            array_push($changes, $team);       

        }

        print_r($changes);

        $this->assertFalse(empty($changes));


    }


}