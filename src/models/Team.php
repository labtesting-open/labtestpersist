<?php   

    namespace Elitelib;

    class Team extends Connect{
       
        private $folder_team ="imgs/teams_profile/";   
        private $path_flag ="imgs/svg/";
        private $folder_club ="imgs/clubs_logo/";   


        public function getTeams($club_id, $country_code = null){
            
            $db = parent::getDataBase(); 

            $country = ($country_code == null)? 'GB': $country_code;

            $query = "
            SELECT
            teams.id team_id,
            IF( ISNULL(teams.team_name), clases.name ,teams.team_name) AS team_name, 
            ct.name as category_name,
            d.name as division_name,
            COALESCE(players_count, 0) AS players,
            COALESCE(age_average, 0) AS age_average,                        
            IF( ISNULL(teams.img_team), null,CONCAT('$this->folder_team', teams.img_team)) AS img_team
            FROM elites17_wizard.teams teams
            LEFT JOIN $db.clubs c ON teams.club_id = c.id
            LEFT JOIN $db.categories ct ON teams.category_id = ct.id
            LEFT JOIN $db.division d ON teams.division_id = d.id
            LEFT JOIN $db.division_class_translate clases ON clases.id = d.division_class_id and clases.country_code='$country'
            LEFT JOIN (
                SELECT  
                players.team_id,
                COUNT(DISTINCT players.id) AS players_count,
                FORMAT(AVG( TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) ), 0) AS age_average
                FROM $db.players players
                WHERE players.club_id=$club_id
                GROUP BY players.team_id
            ) players ON players.team_id = teams.id                        
            WHERE teams.club_id=$club_id" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }      


        public function getPlayerCountAndAvgAge($club_id){

            $db = parent::getDataBase(); 

            $query = "
            SELECT 
            team_id,
            FORMAT(AVG( TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) ), 0) AS age_avg, 
            COUNT(DISTINCT id) AS players_count
            FROM elites17_wizard.players
            WHERE players.club_id=$club_id
            GROUP BY team_id" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

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

            if($category_id != null){
                $where.=( empty($where))?' WHERE ':' and ';
                $where.= " teams.category_id = '$category_id'";
            }

            if($division_id != null){
                $where.=( empty($where))?' WHERE ':' and ';
                $where.= " teams.division_id = '$division_id'";
            }            

            $query = "
            SELECT 
            categories.id,
            categories.name
            FROM $db.teams teams
            INNER JOIN $db.clubs clubs ON clubs.id = teams.club_id
            INNER JOIN $db.categories categories ON categories.id = teams.category_id
            INNER JOIN $db.country_codes countries ON countries.country_code = clubs.country_code
            $where
            group by categories.id           
            ORDER BY categories.name" ;        

            $datos = parent::obtenerDatos($query);    
            
            return $datos;
        }

        public function getAvailableTeamsWithFiltersTotalRows(
            $continent_code = null, 
            $country_code = null, 
            $category_id = null,
            $division_id = null,           
            $order_field = null,
            $order_sense = null,
            $page = null,
            $limit = null,
            $language_code = null
        )
        {
            $db = parent::getDataBase();

            $filters = $this->getFiltersTeamsWithFilters(
                $continent_code, 
                $country_code, 
                $category_id,
                $division_id,                
                $order_field,
                $order_sense,
                $page,
                $limit,
                $language_code
            );

            $mainQuery = $this->getQueryTeamsWithFilters(
                $db,
                $filters["where"],
                $filters["language_code"]
            );

            $query = "SELECT count(*) AS totalrows FROM (
                $mainQuery
                ) AS registros";           

            $datos = parent::obtenerDatos($query);                    
 
            return intval($datos[0]['totalrows']);

        }


        public function getAvailableTeamsWithFilters(
            $continent_code = null, 
            $country_code = null, 
            $category_id = null,
            $division_id = null,           
            $order_field = null,
            $order_sense = null,
            $page = null,
            $limit = null,
            $language_code = null
        )
        {
            $db = parent::getDataBase();

            $filters = $this->getFiltersTeamsWithFilters(
                $continent_code, 
                $country_code, 
                $category_id,
                $division_id,                
                $order_field,
                $order_sense,
                $page,
                $limit,
                $language_code
            );

            $query = $this->getQueryTeamsWithFilters(
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

            $datos = parent::obtenerDatos($query);           
 
            return $datos;


        }        


        public function getTeamsByFilters(
            $continent_code, 
            $country_code, 
            $category_id,
            $division_id,
            $page = 1,
            $cant = 100,
            $order,
            $order_sense,
            $translate_code ='GB'
            ){  

            $db = parent::getDataBase();

            $init = 0;            

            if($page > 1){                
                 $init = ($cant * ($page - 1)) + 1 ;
                 $cant = $cant * $page;
            } 
            
            $where='';            

            if($continent_code != null){
                $where.= "WHERE countries.continent_code='$continent_code'";                   
            }

            if($country_code != null){                
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= "countries.country_code='$country_code'";                    
            }     

            if($category_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= "teams.category_id=$category_id";                    
            }    
            
            if($division_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= "teams.division_id=$division_id";                    
            }  


            switch ($order) {
                
                case 'club_name':
                    $orderfields ="clubs.name";
                    break;
                case 'squad':
                    $orderfields ="squad";
                    break;
                default:
                    $orderfields ="teams.id";
            }            

            $sense = ( $order_sense != null && $order_sense =='ASC')?" ASC ":" DESC ";
            
            $query ="
            SELECT 
            teams.id AS team_id,
            clubs.id AS club_id,
            IFNULL(clases.name,clubs.name) AS team_name,            
            clubs.name AS club_name,            
            IF( ISNULL(clubs.logo), null,CONCAT('$this->folder_club', clubs.logo)) AS logo, 
            categories.name AS category_name,
            divisions.name AS division_name,
            CONCAT(countries.name) AS country_name,
            CONCAT('$this->path_flag',countries.country_code,'.svg') AS country_flag,
            COALESCE(players_count, 0) AS squad
            FROM elites17_wizard.teams teams
            INNER JOIN  elites17_wizard.clubs clubs ON clubs.id = teams.club_id
            INNER JOIN  elites17_wizard.categories categories ON categories.id = teams.category_id
            INNER JOIN  elites17_wizard.division divisions ON divisions.id = teams.division_id
            INNER JOIN  elites17_wizard.country_codes countries ON countries.country_code = clubs.country_code
            LEFT JOIN  elites17_wizard.division_class_translate clases 
            ON clases.id = divisions.division_class_id and clases.country_code='$translate_code'
            LEFT JOIN (
            SELECT   
            players.team_id AS team_id
            ,COUNT(players.id) AS players_count		
            FROM  elites17_wizard.players players   
            GROUP by players.team_id
            ) AS plantilla ON plantilla.team_id = teams.id      
            $where
            ORDER BY $orderfields $sense
            limit $init,$cant
            ";    

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }    


        private function getQueryTeamsWithFilters(
           $db,
           $where,
           $translate_code
        )
        {
            $query ="
            SELECT 
            teams.id AS team_id,
            clubs.id AS club_id,
            IFNULL(clases.name,clubs.name) AS team_name,            
            clubs.name AS club_name,            
            IF( ISNULL(clubs.logo), null,CONCAT('$this->folder_club', clubs.logo)) AS logo, 
            categories.name AS category_name,
            divisions.name AS division_name,
            CONCAT(countries.name) AS country_name,
            CONCAT('$this->path_flag',countries.country_code,'.svg') AS country_flag,
            COALESCE(players_count, 0) AS squad
            FROM $db.teams teams
            INNER JOIN  $db.clubs clubs ON clubs.id = teams.club_id
            INNER JOIN  $db.categories categories ON categories.id = teams.category_id
            INNER JOIN  $db.division divisions ON divisions.id = teams.division_id
            INNER JOIN  $db.country_codes countries ON countries.country_code = clubs.country_code
            LEFT JOIN  $db.division_class_translate clases 
            ON clases.id = divisions.division_class_id and clases.country_code='$translate_code'
            LEFT JOIN (
            SELECT   
            players.team_id AS team_id
            ,COUNT(players.id) AS players_count		
            FROM  $db.players players   
            GROUP by players.team_id
            ) AS plantilla ON plantilla.team_id = teams.id      
            $where
            ";    

            return $query;
        }


        private function getFiltersTeamsWithFilters(
            $continent_code = null, 
            $country_code = null, 
            $category_id = null,
            $division_id = null,           
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

            if($category_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= "teams.category_id=$category_id";                    
            }    
            
            if($division_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= "teams.division_id=$division_id";                    
            }           

            $order = 'teams.id';

            if($order_field != null){
                switch ($order_field) {
                
                    case 'club_name':
                        $order ="clubs.name";
                        break;
                    case 'squad':
                        $order ="squad";
                        break;
                    default:
                        $order ="teams.id";
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



    }