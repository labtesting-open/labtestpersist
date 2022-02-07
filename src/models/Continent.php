<?php

    namespace Elitelib;  


    class Continent extends Connect{      


        public function get($continent_code=null)
        {

            $db = parent::getDataBase();

            $where = "";
            
            if($continent_code != null){
                $where.=' WHERE ';                
                $where.= " continents.continent_code = '$continent_code'";
            }

            $query = "
            SELECT 
            continents.continent_code AS continnet_code,
            continents.name
            FROM $db.continents continents
            $where           
            ORDER BY continents.name" ;

            $rows = parent::obtenerDatos($query);    
            
            return $rows;

        }
        
        
        public function getAvailableContinentsWithTeams(
            $continent_code = null, 
            $country_code = null, 
            $category_id = null,
            $division_id = null  
            ){
            
            $db = parent::getDataBase();            

            $where = ($continent_code != null)?"WHERE  continents.continent_code='$continent_code'":"";

            $whereSub = '';

            if( $category_id != null ){
                $whereSub .= ' WHERE ';                
                $whereSub .= " teams.category_id='$category_id'";
            } 

            if( $country_code != null ){
                $whereSub.=(empty($whereSub))?' WHERE ':' and ';
                $whereSub .= " clubs.country_code='$country_code'";
            }

            if( $division_id != null ){
                $whereSub.=(empty($whereSub))?' WHERE ':' and ';
                $whereSub .= " teams.division_id='$division_id'";
            }
                       

            $query = "
            SELECT 
            continents.continent_code,
            continents.name 
            FROM $db.continents continents
            INNER JOIN (
            SELECT   
            DISTINCT(countries.continent_code) AS continent_code		
            FROM  $db.clubs clubs            
            INNER JOIN  $db.country_codes countries ON countries.country_code = clubs.country_code  
            INNER JOIN $db.teams teams ON teams.club_id = clubs.id 
            $whereSub
            ) clubs_countries ON clubs_countries.continent_code = continents.continent_code
            $where
            ORDER BY continents.name" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }      
        



    }
