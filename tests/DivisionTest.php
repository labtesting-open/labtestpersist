<?php

declare(strict_types=1);

use Elitelib\HostConnection;
use PHPUnit\Framework\TestCase;

class DivisionTest extends TestCase{

    private $division;

    public function setUp(): void
    {
        $host = new HostConnection();

        $this->division = new Elitelib\Division($host->getParams());
    }

    public function testGetAll(){       

        $data_division = $this->division->getAll();
        
        //var_dump($data_division);

        $this->assertFalse(empty($data_division)); 
      
    }


    public function testGetDivision(){  
        
        $division_id= 1;

        $data_division = $this->division->getDivision($division_id);
        
        //var_dump($data_division);

        $this->assertFalse(empty($data_division)); 
      
    }


    public function testGetAvailableDivisions(){  
        
        $continent_code=null;
        $country_code=null;
        $category_id = null;
        $division_id = null;

        $data_division = $this->division->getAvailableDivisions(
            $continent_code,
            $country_code,
            $category_id,
            $division_id
        );
        
        //var_dump($data_division);

        $this->assertFalse(empty($data_division)); 
      
    }


    public function testGetAllDivisions(){ 
        
        
        $country_code='AR';
        $category_id = 1;       

        $data_division = $this->division->getAllDivisions($country_code, $category_id);
        
        var_dump($data_division);

        $this->assertFalse(empty($data_division)); 
      
    }   



}
