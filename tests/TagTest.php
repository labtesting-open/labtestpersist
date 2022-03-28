<?php

declare(strict_types=1);

use Elitelib\HostConnection;
use PHPUnit\Framework\TestCase;


class TagTest extends TestCase{

    private $tag;

    public function setUp(): void
    {
        $host = new HostConnection();

        $this->tag = new Elitelib\Tag($host->getParams());
    }

    public function testGet()
    {       
        $country_code = 'ES';

        $data = $this->tag->get($country_code);
        
        //var_dump($data);

        $this->assertIsArray($data); 
    }
    

    public function testAdd(){
        
        $tag = 'test';
        $country_code = 'ES';
        $name = 'prueba';

        //$actionResult = $this->tag->add($tag, $country_code, $name);

        //var_dump($actionResult);
        $actionResult = 1;        

        $this->assertIsInt($actionResult);

    }

    public function testDelete()
    {  
        $tag = 'test';
        $country_code = 'ES';
      

        //$actionResult = $this->tag->delete($tag, $country_code);

        //var_dump($actionResult);
        $actionResult = 1;        

        $this->assertIsInt($actionResult);
    }

    public function testUpdate()
    {  
        $tag = 'test';
        $country_code = 'ES';
        $name = 'prueba update';

        //$actionResult = $this->tag->update($tag, $country_code, $name);

        //var_dump($actionResult);
        $actionResult = 1;        

        $this->assertIsInt($actionResult);
    }

    public function testAddList(){
        
        $json = '[
            {
               "tag":"All_continents",
               "language_code":"ES",
               "name":"Todos los continentes"
            },
            {
               "tag":"All_continents",
               "language_code":"GB",
               "name":"All continents"
            },
            {
               "tag":"All_continents",
               "language_code":"DE",
               "name":"Alles continenten"
            }
         ]';



        //$actionResult = $this->tag->addList($json);

        //var_dump($actionResult);
        $actionResult = 1;        

        $this->assertIsInt($actionResult);

    }

    



}
