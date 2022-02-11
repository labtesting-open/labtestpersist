<?php

    namespace Elitelib;  

    class Club extends Connect{          


        public function getClubsByFilters(
            $continent_code, 
            $country_code, 
            $category_id,
            $division_id,
            $page = 1,
            $cant = 100
            ){  

            $db = parent::getDataBase();
            
            $imgFolderClub = $this->getImgFolderClubs();
            $imgFolderFlag = $this->getImgFolderFlags();

            $init = 0;            

            if($page > 1){                
                 $init = ($cant * ($page - 1)) + 1 ;
                 $cant = $cant * $page;
            }       
            
            $select ="
            SELECT
            clubs.id,
            clubs.name,            
            IF( ISNULL(clubs.logo), null,CONCAT('$imgFolderClub', clubs.logo)) AS logo,
            clubs.stadium,
            CONCAT(countries.name) AS country_name,
            CONCAT('$imgFolderFlag',countries.country_code,'.svg') AS country_flag,
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
                IF( ISNULL(clubs.logo), null,CONCAT('$imgFolderClub', clubs.logo)) AS logo,
                clubs.stadium,
                CONCAT(countries.name) AS country_name,
                CONCAT('$imgFolderFlag',countries.country_code,'.svg') AS country_flag
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
            $imgFolderClub = $this->getImgFolderClubs();
            $imgFolderFlag = $this->getImgFolderFlags();

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
            IF( ISNULL(clubs.logo), null,CONCAT('$imgFolderClub', clubs.logo)) AS logo, 
            clubs.stadium,
            clubs.since,
            CONCAT(countries.name) AS nacionalities_names,
			CONCAT('$imgFolderFlag',countries.country_code,'.svg') AS nacionalities_flags
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
            $imgFolderClub = $this->getImgFolderClubs();
            $imgFolderFlag = $this->getImgFolderFlags();

            $findScape = parent::scapeParameter($find);

            $query = "
            SELECT
            clubs.id,
            clubs.name,
            IF( ISNULL(clubs.logo), null,CONCAT('$imgFolderClub', clubs.logo)) AS logo,              
            CONCAT(countries.name) AS nacionalities_names,
			CONCAT('$imgFolderFlag',countries.country_code,'.svg') AS nacionalities_flags
            FROM $db.clubs clubs
            LEFT JOIN $db.country_codes countries
			ON countries.country_code = clubs.country_code
            WHERE LOWER(clubs.name) like LOWER('%$findScape%')            
            limit $limit" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }
        
        public function getAvailablesDivisionsForClub($club_id)
        {
            $db = parent::getDataBase(); 
            $imgFolderDivisionLogo = $this->getImgFolderClubs();

            $query="
            SELECT 
            division.name as division_name,
            CONCAT('$imgFolderDivisionLogo',division.logo,'.svg') AS country_flag,
            division.logo
            FROM
            elites17_wizard.teams team
            INNER JOIN elites17_wizard.division division ON division.id = team.division_id
            WHERE team.club_id=$club_id";

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }


        public function getBasicInfo($club_id){
            
            $db = parent::getDataBase(); 
            $imgFolderClub = $this->getImgFolderClubs();
            $imgFolderFlags = $this->getImgFolderFlags();

            $query = "
            SELECT
            clubs.id,
            clubs.name club_name, 
            clubs.stadium,
            clubs.since,
            IF( ISNULL(clubs.logo), null,CONCAT('$imgFolderClub', clubs.logo)) AS logo,             
            CONCAT(countries.name) AS country_name,
            CONCAT('$imgFolderFlags',countries.country_code,'.svg') AS country_flag,
            COALESCE(players_count, 0) AS squad,
            COALESCE(teams_count, 0) AS teams_cant
            FROM  $db.clubs clubs
            INNER JOIN  $db.country_codes countries ON countries.country_code = clubs.country_code
            LEFT JOIN (
                SELECT   
                players.club_id AS club_id
                ,COUNT(players.id) AS players_count		
                FROM  $db.players players   
                where players.club_id = 1
            ) AS plantilla ON plantilla.club_id = clubs.id
            LEFT JOIN (
                SELECT   
                teams.club_id AS club_id,
                COUNT(teams.id) AS teams_count		
                FROM  $db.teams teams   
                where teams.club_id = 1
                )AS teams ON teams.club_id = clubs.id    
            where clubs.id=$club_id";        

            $datos = parent::obtenerDatos($query);           

            return $datos;

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

        
        
        public function getAvailableClubs(
            $continent_code=null, 
            $country_code = null, 
            $category_id = null,
            $division_id = null,
            $club_id = null,
            $nacionality_code = null, 
            $orderField = null,
            $orderSense = null
        ){
            
            $db = parent::getDataBase();
            
            $imgFolderClub = $this->getImgFolderClubs();  
            
            $whereSubQuery="";

            if($continent_code != null){
                $whereSubQuery.=' WHERE ';                
                $whereSubQuery.= " countries.continent_code = '$continent_code'";
            }

            if($country_code != null){
                $whereSubQuery.=(empty($whereSubQuery))?' WHERE ':' and ';
                $whereSubQuery.= " countries.country_code = '$country_code'";
            }

            if($category_id != null){
                $whereSubQuery.=(empty($whereSubQuery))?' WHERE ':' and ';
                $whereSubQuery.= " teams.category_id = $category_id";
            }           

            if($division_id != null){
                $whereSubQuery.=(empty($whereSubQuery))?' WHERE ':' and ';
                $whereSubQuery.= " teams.division_id = $division_id";
            }

            if($club_id != null){
                $whereSubQuery.=(empty($whereSubQuery))?' WHERE ':' and ';
                $whereSubQuery.= " clubs.id = $club_id";
            }  
            
            $where = "";

            if($nacionality_code != null){
                $where.=' WHERE ';                
                $where.= " nacionalities.country_code = '$nacionality_code'";
            }           

            $order = 'clubs.name';

            if($orderField != null){
                $order = $orderField;
            }
            
            $sense = (isset($orderSense) && $orderSense !='ASC')?'DESC':'ASC';


            $query = "
            SELECT
            clubs.id,            
            clubs.name,
            CONCAT('$imgFolderClub', clubs.logo) AS logo
            FROM  $db.players players
            INNER JOIN (
                SELECT
                clubs.id,
                clubs.name,
                clubs.logo,
                teams.category_id,
                teams.division_id    
                FROM  $db.clubs clubs
                INNER JOIN  $db.country_codes countries ON countries.country_code = clubs.country_code
                LEFT JOIN (
                    SELECT   
                    teams.club_id AS club_id,
                    teams.category_id,
                    teams.division_id
                    FROM  $db.teams teams 
                )AS teams ON teams.club_id = clubs.id
                $whereSubQuery
                GROUP BY clubs.id
            ) AS clubs ON clubs.id = players.club_id
            LEFT JOIN (
            SELECT 
            nacionalities.player_id,
            nacionalities.country_code ,
            country_codes.name
            FROM $db.players_nacionalities nacionalities 
            LEFT JOIN $db.country_codes country_codes 
            ON country_codes.country_code = nacionalities.country_code
            ) AS nacionalities ON nacionalities.player_id = players.id
            $where
            GROUP BY players.club_id
            ORDER BY $order $sense";        

            $datos = parent::obtenerDatos($query);           
 
            return $datos;

        }


        private function getFiltersClubsWithFilters(
            $continent_code = null, 
            $country_code = null,
            $order_field = null,
            $order_sense = null,
            $page = null,
            $limit = null,
            $language_code = null
        )
        { 

            $where = "";

            if($continent_code != null){
                $where.= "WHERE countries.continent_code='$continent_code'";                   
            }

            if($country_code != null){                
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= "countries.country_code='$country_code'";                    
            }              

            $order = 'club_name';

            if($order_field != null){
                switch ($order_field) {
                
                    case 'club_name':
                        $order ="clubs.name";
                        break;
                    case 'squad':
                        $order ="squad";
                        break;                   
                    default:
                        $order ="clubs.name";
                }
            }
            
            $sense = ( $order_sense != null && $order_sense =='ASC')?" ASC ":" DESC ";

            $language_code = (isset($language_code) && $language_code != null)? $language_code: 'GB';

            $page = (isset($page) && $page != null)? $page : 1;
            $cant = (isset($limit)&& $limit != null)? $limit :100;            

            $offset = ($page - 1) * $cant;

            $filters = array(
                "language_code"=> $language_code,
                "where" => $where,
                "order" => $order,
                "sense" => $sense,
                "offset" => $offset,
                "cant" => $cant
            );

            return $filters;

        }


        private function getQueryClubsWithFilters(
            $db,
            $where,
            $translate_code
         )
         {
             
             $imgFolderClub = $this->getImgFolderClubs();
             $imgFolderFlags = $this->getImgFolderFlags();
 
             $query ="
             SELECT
             clubs.id,
             clubs.name AS club_name,            
             IF( ISNULL(clubs.logo), null,CONCAT('$imgFolderClub', clubs.logo)) AS logo,             
             CONCAT(countries.name) AS country_name,
             CONCAT('$imgFolderFlags',countries.country_code,'.svg') AS country_flag,
             COALESCE(players_count, 0) AS squad,
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
             $where
             ";    
 
             return $query;
         }
        
        
        public function getAvailableClubsWithFilters(
            $continent_code = null, 
            $country_code = null,                       
            $order_field = null,
            $order_sense = null,
            $page = null,
            $limit = null,
            $language_code = null
        )
        {
            $db = parent::getDataBase();

            $filters = $this->getFiltersClubsWithFilters(
                $continent_code, 
                $country_code,                              
                $order_field,
                $order_sense,
                $page,
                $limit,
                $language_code
            );

            $query = $this->getQueryClubsWithFilters(
                $db,
                $filters["where"],
                $filters["language_code"]
            );

            $order = $filters["order"];
            $sense = $filters["sense"];
            $offset = $filters["offset"];
            $cant = $filters["cant"];

            $query.= " 
            ORDER BY $order $sense 
            LIMIT $offset,$cant";           

            $rows = parent::obtenerDatos($query);           
 
            return $rows;


        } 
        
        
        public function getAvailableClubsWithFiltersTotalRows(
            $continent_code = null, 
            $country_code = null,
            $order_field = null,
            $order_sense = null,
            $page = null,
            $limit = null,
            $language_code = null
        )
        {
            $db = parent::getDataBase();

            $filters = $this->getFiltersClubsWithFilters(
                $continent_code, 
                $country_code,                                 
                $order_field,
                $order_sense,
                $page,
                $limit,
                $language_code
            );

            $mainQuery = $this->getQueryClubsWithFilters(
                $db,
                $filters["where"],
                $filters["language_code"]
            );

            $query = "SELECT count(*) AS totalrows FROM (
                $mainQuery
                ) AS registros";           

            $rows = parent::obtenerDatos($query);                    
 
            return intval($rows[0]['totalrows']);

        }



        



    }