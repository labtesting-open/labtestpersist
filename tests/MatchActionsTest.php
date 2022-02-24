<?php

declare(strict_types=1);

use Elitelib\HostConnection;
use PHPUnit\Framework\TestCase;



class MatchActionsTest extends TestCase{

    private $matchActions;

    public function setUp(): void
    {
        $host = new HostConnection();

        $this->matchActions = new Elitelib\MatchActions($host->getParams());
    }

    public function testGetSeasons(){

        $player_id = 1;
        $season_id = null;
        $match_id = 1;
        $order = null;
        $order_sense = null;

        $seasons = $this->matchActions->getSeasons(
            $player_id,
            $season_id,
            $match_id,
            $order,
            $order_sense
        );
        
        //var_dump($seasons);

        $this->assertFalse(empty($seasons)); 
      
    }

    public function testGetMatches(){

        $player_id = 1;
        $season_id = null;
        $match_id = null;
        $order = null;
        $order_sense = null;

        $seasons = $this->matchActions->getMatches(
            $player_id,
            $season_id,
            $match_id,
            $order,
            $order_sense
        );
        
        //var_dump($seasons);

        $this->assertFalse(empty($seasons)); 
      
    }

    public function testGetActions(){

        $player_id = 1;
        $season_id = null;
        $match_id = null;
        $order = null;
        $order_sense = null;

        $seasons = $this->matchActions->getActions(
            $player_id,
            $season_id,
            $match_id,
            $order,
            $order_sense
        );
        
        //var_dump($seasons);

        $this->assertFalse(empty($seasons)); 
      
    }


    
    

}
