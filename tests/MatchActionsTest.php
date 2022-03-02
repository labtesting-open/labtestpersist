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
        $match_id = null;
        $order = null;
        $order_sense = null;
        $season_id_select = null;

        $seasons = $this->matchActions->getSeasons(
            $player_id,
            $season_id,
            $season_id_select,
            $match_id,
            $order,
            $order_sense
        );
        
        var_dump($seasons);

        $this->assertFalse(empty($seasons)); 
      
    }

    public function testGetMatches(){

        $player_id = 1;
        $season_id = null;
        $match_id = null;
        $order = null;
        $order_sense = null;
        $match_id_select_list = null;

        $seasons = $this->matchActions->getMatches(
            $player_id,
            $season_id,
            $match_id,
            $match_id_select_list,
            $order,
            $order_sense
        );
        
        //var_dump($seasons);

        $this->assertFalse(empty($seasons)); 
      
    }

    public function testGetActions(){

        $player_id = 1;
        $season_id = null;
        $match_id_list = null;
        $action_id_select_list = null;
        $order = null;
        $order_sense = null;
        

        $seasons = $this->matchActions->getActions(
            $player_id,
            $season_id,
            $match_id_list,
            $action_id_select_list,
            $order,
            $order_sense
        );
        
        //var_dump($seasons);

        $this->assertFalse(empty($seasons)); 
      
    }

    public function testGetPlayerActions(){

        $player_id = 1;
        $season_id = null;
        $match_id_list = null;
        $action_id_select_list = null;
        $order = null;
        $order_sense = null;
        $user_id = 1;
        $totalAccounts = 6;

        $seasons = $this->matchActions->getPlayerActions(
            $player_id,
            $season_id,
            $match_id_list,
            $action_id_select_list,
            $order,
            $order_sense,
            $user_id,
            $totalAccounts
        );
        
        //var_dump($seasons);

        $this->assertFalse(empty($seasons)); 
      
    }


    
    

}
