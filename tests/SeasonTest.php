<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;


class SeasonTest extends TestCase{

    private $season;

    public function setUp(): void
    {
        $this->season = new Elitelib\Season();
    }

    public function testGetAllSeasons()
    {       
        $data = $this->season->getAllSeasons();
        
        //var_dump($data);

        $this->assertFalse(empty($data)); 
    }

    public function testgetSeasonsWithMatchesByClubTeam()
    {       
        $club_id = 1;
        $team_id = 1;
        $onlySeasonsWithMatches = true;

        $data = $this->season->getSeasonsWithMatchesByClubTeam($club_id, $team_id, $onlySeasonsWithMatches);
        
        //var_dump($data);

        $this->assertFalse(empty($data)); 

    }


}
