<?php

declare(strict_types=1);

use Elitelib\HostConnection;
use PHPUnit\Framework\TestCase;

class CategoryTest extends TestCase{

    private $category;

    public function setUp(): void
    {
        $host = new HostConnection();

        $this->category = new Elitelib\Category($host->getParams());
    }

    public function testGetAll(){       

        $data_category = $this->category->getAll();
        
        //var_dump($data_category);

        $this->assertFalse(empty($data_category)); 
      
    }


    public function testGetCategory(){  
        
        $category_id= 1;

        $data_category = $this->category->getCategory($category_id);
        
        //var_dump($data_category);

        $this->assertFalse(empty($data_category)); 
      
    }


    public function testGetAvailableCategories(){  
        
        $continent_code=null;
        $country_code=null;
        $category_id = null;
        $division_id = null;

        $data_category = $this->category->getAvailableCategories(
            $continent_code,
            $country_code,
            $category_id,
            $division_id
        );
        
        //var_dump($data_category);

        $this->assertFalse(empty($data_category)); 
      
    }


}
