<?php

    namespace Elitelib;  


    class Category extends Connect{      


        public function getAll()
        {

            $db = parent::getDataBase();            

            $query = "
            SELECT 
            categories.id AS category_id,
            categories.name
            FROM $db.categories categories
            
            ORDER BY categories.order_class" ;

            $rows = parent::obtenerDatos($query);    
            
            return $rows;

        }


        public function getCategory($category_id)
        {

            $db = parent::getDataBase();            

            $query = "
            SELECT 
            categories.id AS category_id,
            categories.name,
            categories.order_class
            FROM $db.categories categories
            WHERE id=$category_id" ;

            $rows = parent::obtenerDatos($query);    
            
            return $rows;

        }


        public function getAvailableCategories(
            $continent_code=null, 
            $country_code=null, 
            $category_id = null,
            $division_id = null
        ){

            $db = parent::getDataBase();  
            
            $where = "";
            
            if($continent_code != null){
                $where.=' WHERE ';                
                $where.= " countries.continent_code = '$continent_code'";
            }

            if($country_code != null){
                $where.=( empty($where))?' WHERE ':' and ';
                $where.= " clubs.country_code = '$country_code'";
            }            

            if($division_id != null){
                $where.=( empty($where))?' WHERE ':' and ';
                $where.= " teams.division_id = '$division_id'";
            }
            
            $selected = '';

            if ($category_id != null) {
                $selected =",IF(categories.id=$category_id, 'true','false') AS selected ";
            }

            $query = "
            SELECT 
            categories.id,
            categories.name
            $selected
            FROM $db.teams teams
            INNER JOIN $db.clubs clubs ON clubs.id = teams.club_id
            INNER JOIN $db.categories categories ON categories.id = teams.category_id
            INNER JOIN $db.country_codes countries ON countries.country_code = clubs.country_code
            $where
            group by categories.id           
            ORDER BY categories.name" ;        

            $rows = parent::obtenerDatos($query);    
            
            return $rows;
        }

       


        



    }
