<?php

declare(strict_types=1);

use Elitelib\HostConnection;
use PHPUnit\Framework\TestCase;

class CountryTest extends TestCase{

    private $country;

    public function setUp(): void
    {
        $host = new HostConnection();

        $this->country = new Elitelib\Country($host->getParams());
    }

    public function testGetAll(){       

        $data_country = $this->country->get();
        
        //var_dump($data_country);

        $this->assertFalse(empty($data_country)); 
      
    }


    public function testGetCategory(){  
        
        $country_code= 'AR';

        $data_country = $this->country->get($country_code);
        
        //var_dump($data_country);

        $this->assertFalse(empty($data_country)); 
      
    }


    public function testGetAvailableCountriesWithTeams(){  
        
        $continent_code=null;
        $country_code=null;
        $category_id = null;
        $division_id = null;

        $data_country = $this->country->getAvailableCountriesWithTeams(
            $continent_code,
            $country_code,
            $category_id,
            $division_id
        );
        
        //var_dump($data_country);

        $this->assertFalse(empty($data_country)); 
      
    }


}
