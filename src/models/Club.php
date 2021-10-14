<?php

    namespace Elitelib;  

    class Club extends Connect{
               
        private $path_flag ="imgs/svg/";
        private $folder_club="imgs/clubs_logo/";  


        public function getClubsByFilters(
            $continent_code, 
            $country_code, 
            $category_id,
            $division_id,
            $page = 1,
            $cant = 100
            ){  

            $db = parent::getDataBase();

            $init = 0;            

            if($page > 1){                
                 $init = ($cant * ($page - 1)) + 1 ;
                 $cant = $cant * $page;
            }       
            
            $select ="
            SELECT
            clubs.id,
            clubs.name,            
            IF( ISNULL(clubs.logo), null,CONCAT('$this->folder_club', clubs.logo)) AS logo,
            clubs.stadium,
            CONCAT(countries.name) AS country_name,
            CONCAT('$this->path_flag',countries.country_code,'.svg') AS country_flag,
            COALESCE(players_count, 0) AS plantilla,
            COALESCE(teams_count, 0) AS teams_cant
            FROM  $db.clubs clubs
            INNER JOIN  $db.country_codes countries ON countries.country_code = clubs.country_code
            LEFT JOIN (
                SELECT   
                players.club_id AS club_id
                ,COUNT(players.id) AS players_count		
                FROM  $db.players players   
                GROUP by players.club_id
            ) AS plantilla ON plantilla.club_id = clubs.id
            LEFT JOIN (
                SELECT   
                teams.club_id AS club_id,
                COUNT(teams.id) AS teams_count		
                FROM  $db.teams teams   
                GROUP by teams.club_id
                )AS teams ON teams.club_id = clubs.id
            ";

            $where='';
            $order=" ORDER BY clubs.id DESC";

            if($continent_code != null){
                $where.= "WHERE countries.continent_code='$continent_code'";                   
            }

            if($country_code != null){
                $where.=($where == '')?' WHERE ':' and ';
                $where.= "countries.country_code='$country_code'";                    
            }       

            if ($division_id != null || $category_id != null){

                $where.=($where == '')?' WHERE ':' and ';
                $where.= "teams.id='$division_id'"; 

                if( $category_id != null ){
                    $where.=($where == '')?' WHERE ':' and ';
                    $where.= "teams.category_id='$category_id'";
                }

                $select ="
                SELECT 
                clubs.id,
                clubs.name,
                IF( ISNULL(clubs.logo), null,CONCAT('$this->folder_club', clubs.logo)) AS logo,
                clubs.stadium,
                CONCAT(countries.name) AS country_name,
                CONCAT('$this->path_flag',countries.country_code,'.svg') AS country_flag
                FROM $db.teams teams
                INNER JOIN $db.clubs clubs ON clubs.id = teams.club_id
                INNER JOIN $db.division divisions ON divisions.id = teams.division_id
                INNER JOIN  $db.country_codes countries ON countries.country_code = clubs.country_code";

                $order ="ORDER BY clubs.name ASC";

            }

            $query = "
            $select
            $where  
            $order                               
            limit $init,$cant" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }      
       

        public function findClubs($find, $language_code, $page = 1, $limit=100){  

            $db = parent::getDataBase();
            $init = 0;
            

            if($page > 1){                
                $init = ($limit * ($page - 1)) + 1 ;
                $limit = $limit * $page;
            }

            $findScape = parent::scapeParameter($find);

            $query = "
            SELECT
            clubs.id,
            clubs.name,
            IF( ISNULL(clubs.logo), null,CONCAT('$this->folder_club', clubs.logo)) AS logo, 
            clubs.stadium,
            clubs.since,
            CONCAT(countries.name) AS nacionalities_names,
			CONCAT('$this->path_flag',countries.country_code,'.svg') AS nacionalities_flags
            FROM $db.clubs clubs
            LEFT JOIN $db.country_codes countries
			ON countries.country_code = clubs.country_code
            WHERE LOWER(clubs.name) like LOWER('%$findScape%')            
            limit $init,$limit" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }      


        public function findClubsFast($find, $language_code, $limit = 10){  

            $db = parent::getDataBase();

            $findScape = parent::scapeParameter($find);

            $query = "
            SELECT
            clubs.id,
            clubs.name,
            IF( ISNULL(clubs.logo), null,CONCAT('$this->folder_club', clubs.logo)) AS logo,              
            CONCAT(countries.name) AS nacionalities_names,
			CONCAT('$this->path_flag',countries.country_code,'.svg') AS nacionalities_flags
            FROM $db.clubs clubs
            LEFT JOIN $db.country_codes countries
			ON countries.country_code = clubs.country_code
            WHERE LOWER(clubs.name) like LOWER('%$findScape%')            
            limit $limit" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }      


        public function getBasicInfo($club_id){
            
            $db = parent::getDataBase(); 

            $query = "
            SELECT
            id,
            name,
            IF( ISNULL(logo), null,CONCAT('$this->folder_club', logo)) AS logo,
            stadium,
            since,
            country_code,
            (
                SELECT   
                COUNT(players.id) AS players_count		
                FROM  $db.players players   
                WHERE players.club_id=$club_id
            ) AS players_cant,
            (
                SELECT   
                COUNT(teams.id) AS teams_count		
                FROM  $db.teams teams   
                WHERE teams.club_id=$club_id
            ) AS teams_cant
            FROM $db.clubs            
            where id=$club_id" ;        

            $datos = parent::obtenerDatos($query);           

            return (count($datos) > 0)? $datos[0]: null;

        }      


        public function getAvailableContinents(
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


        public function getAllCountries(){

            $db = parent::getDataBase(); 

            $query = "
            SELECT 
            countries.name as name,
            CONCAT('$this->path_flag',countries.country_code,'.svg') AS country_flag,
            countries.continent_code 
            FROM  $db.country_codes countries 
            ORDER BY countries.name" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;
        }


        public function getAvailableCountries(
            $continent_code = null, 
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
            CONCAT('$this->path_flag',countries.country_code,'.svg') AS country_flag,
            countries.continent_code 
            FROM  $db.clubs clubs            
            INNER JOIN  $db.country_codes countries ON countries.country_code = clubs.country_code                       
            INNER JOIN elites17_wizard.teams teams ON teams.club_id = clubs.id
            $where
            ORDER BY countries.name" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

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
            FROM elites17_wizard.teams teams
            INNER JOIN $db.clubs clubs ON clubs.id = teams.club_id
            INNER JOIN $db.division divisions ON divisions.id = teams.division_id  
            INNER JOIN $db.country_codes countries ON countries.country_code = clubs.country_code         
            $where
            ORDER BY divisions.name" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }
        
        public function getAvailableClubs(
            $continent_code=null, 
            $country_code = null, 
            $category_id = null,
            $division_id = null,
            $club_id = null,
            $orderField = 'clubs.name',
            $orderSense = 'ASC'
        ){
            
            $db = parent::getDataBase();  
            
            $where = "";

            if($continent_code != null){
                $where.=' WHERE ';                
                $where.= " countries.continent_code = '$continent_code'";
            }

            if($country_code != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= " countries.country_code = '$country_code'";
            }

            if($category_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= " teams.category_id = $category_id";
            }           

            if($division_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= " teams.division_id = $division_id";
            }

            if($club_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= " clubs.id = $club_id";
            }            

            $query = "
            SELECT
            clubs.id,
            clubs.name,
            teams.category_id,
            teams.division_id          
            FROM  elites17_wizard.clubs clubs
            INNER JOIN  $db.country_codes countries ON countries.country_code = clubs.country_code
             LEFT JOIN (
                SELECT   
                teams.club_id AS club_id,
                teams.category_id,
                teams.division_id
                FROM  $db.teams teams 
            )AS teams ON teams.club_id = clubs.id
            $where
            GROUP BY clubs.id
            ORDER BY $orderField $orderSense
            " ;

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }      



        



    }