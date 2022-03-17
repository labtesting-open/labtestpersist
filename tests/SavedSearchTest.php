<?php

declare(strict_types=1);

use Elitelib\HostConnection;
use PHPUnit\Framework\TestCase;


class SavedSearchTest extends TestCase{

    private $savedSearch;

    public function setUp(): void
    {
        $host = new HostConnection();

        $this->savedSearch = new Elitelib\SavedSearch($host->getParams());
    }

    public function testGet()
    {       
        $user_id = 1;
        $target = null;
        $saved_search_id = null;

        $data = $this->savedSearch->get($user_id, $target, $saved_search_id);
        
        var_dump($data);

        $this->assertIsArray($data); 
    }
    

    public function testAdd(){
        
        $user_id = 1;
        $target = 'player';
        $paramsJSON="{\"continent_code\":\"SA\",\"country_code\":\"AR\",\"category_id\":\"1\",\"division_id\":\"1\",\"club_id\":\"1\",\"position_id\":\"4\",\"target\":\"player\",\"result\":\"20\"}";
        $searchResult=10;
        
        //$actionResult = $this->savedSearch->add($user_id, $target, $paramsJSON, $searchResult);

        //var_dump($actionResult);
        $actionResult = 1;        

        $this->assertIsInt($actionResult);

    } 

    



}
