<?php

    namespace Elitelib;  


    class Division extends Connect{      


        public function getAll()
        {
            $db = parent::getDataBase();           

            $query = "
            SELECT 
            division.id AS division_id,
            division.name 
            FROM $db.division division           
            ORDER BY division.division_class_id" ;

            $rows = parent::obtenerDatos($query);    
            
            return $rows;
        }


        public function getDivision($division_id)
        {
            $db = parent::getDataBase();           

            $query = "
            SELECT 
            division.id AS division_id,
            division.name 
            FROM $db.division division           
            WHERE division.id=$division_id" ;

            $rows = parent::obtenerDatos($query);    
            
            return $rows;
        }


        public function getAvailableDivisions(
            $continent_code=null, 
            $country_code = null, 
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
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= " divisions.country_code = '$country_code'";
            }

            if($category_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= " teams.category_id = $category_id";
            }           

            if($division_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= " teams.division_id = $division_id";
            }
            

            $query = "
            SELECT 
            DISTINCT(teams.division_id) AS division_id,
            divisions.name,            
            clubs.country_code,
            teams.category_id
            FROM $db.teams teams
            INNER JOIN $db.clubs clubs ON clubs.id = teams.club_id
            INNER JOIN $db.division divisions ON divisions.id = teams.division_id  
            INNER JOIN $db.country_codes countries ON countries.country_code = clubs.country_code         
            $where
            ORDER BY divisions.name" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }


        public function getAllDivisionsFromCountry($country_code = null)
        {

            $db = parent::getDataBase(); 

            $where = "";

            if($country_code != null){
                $where.=( empty($where))?' WHERE ':' and ';
                $where.= " division.country_code = '$country_code'";
            }   

            $query = "
            SELECT 
            division.id AS division_id,
            division.name 
            FROM $db.division division
            $where
            ORDER BY division.division_class_id" ;

            $rows = parent::obtenerDatos($query);    
            
            return $rows;

        }


        public function getAllDivisionsFromCategory($category_id)
        {

            $db = parent::getDataBase();         

            $query = "
            SELECT 
            division.id AS division_id,
            division.name 
            FROM $db.division division
            WHERE division.category_id = $category_id
            ORDER BY division.division_class_id" ;

            $rows = parent::obtenerDatos($query);    
            
            return $rows;

        }
       


        



    }
