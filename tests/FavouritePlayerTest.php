<?php

declare(strict_types=1);

use Elitelib\HostConnection;
use PHPUnit\Framework\TestCase;


class FavouritePlayerTest extends TestCase{

    private $favourite;

    public function setUp(): void
    {
        $host = new HostConnection();

        $this->favourite = new Elitelib\FavouritePlayer($host->getParams());
    }

    public function testGetFavouritePlayersByUser()
    {       
        $user_id = 1;

        $data = $this->favourite->getFavouritePlayersByUser($user_id);
        
        //var_dump($data);

        $this->assertIsArray($data); 
    }
    

    public function testAddFavourite(){
        
        $user_id = 1;
        $player_id = 2;
        
        //$actionResult = $this->favourite->add($user_id, $player_id);

        //var_dump($actionResult);
        $actionResult = 1;        

        $this->assertIsInt($actionResult);

    }


    public function testDeleteFavourite(){
        
        $user_id = 1;
        $player_id = 1;
        
        //$actionResult = $this->favourite->delete($user_id, $player_id);
        
        //var_dump($actionResult);        
        
        $actionResult = 1;
        
        $this->assertIsInt($actionResult);

    }

    public function testAddFavouriteList(){
        
        $user_id = 1;
        $playerListArray = array(100,105,110);
        
        $actionResult = $this->favourite->addList($user_id, $playerListArray);

        //var_dump($actionResult);
        //$actionResult = 1;        

        $this->assertIsInt($actionResult);

    }

    public function testDeleteFavouriteList(){
        
        $user_id = 1;
        $playerListArray = array(100,105,110);
        
        $actionResult = $this->favourite->deleteList($user_id, $playerListArray);

        var_dump($actionResult);
        //$actionResult = 1;        

        $this->assertIsInt($actionResult);

    }    

    public function testGetFavouritePlayers()
    {
        $user_id = 1;
        $player_id = null;
        $orderField = null;
        $orderSense = null;
        $page = null;
        $limit = null;
        $language_code = null;
        
        $favouriteList = $this->favourite->getFavouritePlayers(
            $user_id
            ,$player_id
            ,$orderField
            ,$orderSense
            ,$page
            ,$limit
            ,$language_code
        );

        //var_dump($favouriteList);

        $this->assertIsArray($favouriteList);

    }


    public function testGetFavouritePlayersTotalRows()
    {
        $user_id = 0;
        $player_id = null;
        $orderField = null;
        $orderSense = null;
        $page = null;
        $limit = 5;
        $language_code = null;
        
        $pagesRows = $this->favourite->getFavouritePlayersTotalRows(
            $user_id
            ,$player_id
            ,$orderField
            ,$orderSense
            ,$page
            ,$limit
            ,$language_code
        );

        //var_dump($pagesRows);

        $totalPages = ceil($pagesRows / $limit);

        //echo "total rows: $pagesRows - total pages: $totalPages";

        $this->assertIsInt($pagesRows);

    }

    public function testGetPlayerNews()
    {
        $user_id = 1;       
        $language_code = 'ES';
        
        $playerNews = $this->favourite->getPlayerNews(
            $user_id,           
            $language_code
        );

        //var_dump($playerNews);

        $this->assertIsArray($playerNews);

    }

    public function testSetActionAsViewed()
    {
        $user_id = 1;       
        $match_action_id = 68;
        
        // $actionResult = $this->favourite->setActionAsViewed(
        //     $user_id,           
        //     $match_action_id
        // );

        // var_dump($actionResult);
        $actionResult = 1;        

        $this->assertIsInt($actionResult);

    }

    public function testSetActionListAsViewed()
    {
        $user_id = 1;       
        $match_action_id_ListArray = array(69,70,71);
        
        // $actionResult = $this->favourite->setActionListAsViewed(
        //     $user_id,           
        //     $match_action_id_ListArray
        // );

        // var_dump($actionResult);
        $actionResult = 1;        

        $this->assertIsInt($actionResult);

    }

    public function testDeleteActionsViewed()
    {
        $user_id = 1;       
        $date_from = '2022-03-11';
        
        // $actionResult = $this->favourite->deleteActionsViewed(
        //     $user_id,           
        //     $date_from
        // );

        // var_dump($actionResult);
        $actionResult = 1;        

        $this->assertIsInt($actionResult);

    }

    public function testUpdateDateChecked()
    {
        $user_id = 1; 
        $player_id = 14;      
        $date_from = '2022-03-11';
        
        // $actionResult = $this->favourite->updateDateChecked(
        //     $user_id,
        //     $player_id,           
        //     $date_from
        // );

        //var_dump($actionResult);
        $actionResult = 1;        

        $this->assertIsInt($actionResult);

    }

    public function testGetTotalNewActions()
    {
        $user_id = 1; 
        
        $totalNewActions = $this->favourite->getTotalNewActions($user_id);

        //var_dump($totalNewActions);

        $this->assertIsInt($totalNewActions);

    }


    public function testSetPlayerListAsViewed()
    {
        $user_id = 1;       
        $player_id_ListArray = null;
        $dateChecked = null;
        
        $actionResult = $this->favourite->setPlayerListAsViewed(
            $user_id,           
            $player_id_ListArray,
            $dateChecked
        );

        var_dump($actionResult);
        //$actionResult = 1;        

        $this->assertIsInt($actionResult);

    }

    



}
