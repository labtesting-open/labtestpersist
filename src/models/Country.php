<?php

    namespace Elitelib;  


    class Country extends Connect{ 
        


        public function get($country_code=null)
        {

            $db = parent::getDataBase();
            
            $imgFolderFlag = $this->getImgFolderFlags();

            $where = "";
            
            if($country_code != null){
                $where.=' WHERE ';                
                $where.= " countries.country_code = '$country_code'";
            }

            $query = "
            SELECT
            countries.country_code,
            countries.name,
            CONCAT('$imgFolderFlag',countries.country_code,'.svg') AS country_flag 
            FROM $db.country_codes countries
            $where            
            ORDER BY countries.name" ;

            $rows = parent::obtenerDatos($query);    
            
            return $rows;

        }


        public function getAvailableCountries(
            $continent_code = null, 
            $country_code = null, 
            $category_id = null,
            $division_id = null
            ){
            
            $db = parent::getDataBase(); 
            $imgFolderFlag = $this->getImgFolderFlags();

            $where = "";

            if($continent_code != null){
                $where.=' WHERE ';                
                $where.= " countries.continent_code = '$continent_code'";
            }  

            if($country_code != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= " clubs.country_code = '$country_code'";
            } 
            
            if($category_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= " teams.category_id = '$category_id'";
            }

            if($division_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= " teams.division_id = '$division_id'";
            }

            $query = "
            SELECT   
            DISTINCT(clubs.country_code) AS country_code,
            countries.name as name,
            CONCAT('$imgFolderFlag',countries.country_code,'.svg') AS country_flag,
            countries.continent_code 
            FROM  $db.clubs clubs            
            INNER JOIN  $db.country_codes countries ON countries.country_code = clubs.country_code                       
            INNER JOIN $db.teams teams ON teams.club_id = clubs.id
            $where
            ORDER BY countries.name" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }
        
        
        public function getAllCountries(){

            $db = parent::getDataBase(); 
            $imgFolderFlag = $this->getImgFolderFlags();

            $query = "
            SELECT 
            countries.name as name,
            CONCAT('$imgFolderFlag',countries.country_code,'.svg') AS country_flag,
            countries.continent_code 
            FROM  $db.country_codes countries 
            ORDER BY countries.name" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;
        }
        



    }
