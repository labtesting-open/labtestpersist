<?php   

    namespace Elitelib;

    class Team extends Connect{        
        
        public function getTeam($team_id)
        {
            $db = parent::getDataBase();

            $imgFolderTeam = parent::getImgFolderTeams();
            $imgFolderFlags = $this->getImgFolderFlags();

            $query="SELECT
            teams.club_id,  
            teams.team_name,
            teams.category_id,
            teams.division_id,
            categories.name as category_name,
            divisions.name as division_name,
            countries.name AS country_name,
            CONCAT('$imgFolderFlags',countries.country_code,'.svg') AS country_flag,
            IF( ISNULL(teams.img_team), null,CONCAT('$imgFolderTeam', teams.img_team)) AS img_team           
            FROM $db.teams teams
            INNER JOIN $db.clubs club ON teams.club_id = club.id
            LEFT JOIN $db.country_codes countries ON countries.country_code = club.country_code
            LEFT JOIN $db.categories categories ON categories.id = teams.category_id
            LEFT JOIN $db.division divisions ON divisions.id = teams.division_id
            WHERE teams.id=$team_id";

            $rows = parent::obtenerDatos($query);           

            return $rows;

        }


        public function getTeams($club_id, $country_code = null){
            
            $db = parent::getDataBase();

            $imgFolderTeam = parent::getImgFolderTeams();
            $imgFolderFlags = $this->getImgFolderFlags();    

            $country = ($country_code == null)? 'GB': $country_code;

            $query = "
            SELECT
            teams.id team_id,
            IF( ISNULL(teams.team_name), clases.name ,teams.team_name) AS team_name, 
            ct.name as category_name,
            d.name as division_name,
            COALESCE(players_count, 0) AS squad,
            COALESCE(age_average, 0) AS age_average,
            CONCAT(countries.name) AS country_name,
            CONCAT('$imgFolderFlags',c.country_code,'.svg') AS country_flag,                        
            IF( ISNULL(teams.img_team), null,CONCAT('$imgFolderTeam', teams.img_team)) AS img_team
            FROM $db.teams teams            
            LEFT JOIN $db.clubs c ON teams.club_id = c.id
            LEFT JOIN elites17_wizard.country_codes countries ON countries.country_code = c.country_code
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

            $rows = parent::obtenerDatos($query);           

            return $rows;

        }      


        public function getPlayerCountAndAvgAge($club_id){

            $db = parent::getDataBase(); 

            $query = "
            SELECT 
            team_id,
            FORMAT(AVG( TIMESTAMPDIFF(YEAR, birthdate, CURDATE()) ), 0) AS age_avg, 
            COUNT(DISTINCT id) AS players_count
            FROM $db.players
            WHERE players.club_id=$club_id
            GROUP BY team_id" ;        

            $rows = parent::obtenerDatos($query);           

            return $rows;

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

            $rows = parent::obtenerDatos($query);                    
 
            return intval($rows[0]['totalrows']);

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

            $rows = parent::obtenerDatos($query);           
 
            return $rows;


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

            $imgFolderClub = $this->getImgFolderClubs();
            $imgFolderFlags = $this->getImgFolderFlags();

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
            IF( ISNULL(clubs.logo), null,CONCAT('$imgFolderClub', clubs.logo)) AS logo, 
            categories.name AS category_name,
            divisions.name AS division_name,
            CONCAT(countries.name) AS country_name,
            CONCAT('$imgFolderFlags',countries.country_code,'.svg') AS country_flag,
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
            ORDER BY $orderfields $sense
            limit $init,$cant
            ";    

            $rows = parent::obtenerDatos($query);           

            return $rows;

        }    


        private function getQueryTeamsWithFilters(
           $db,
           $where,
           $translate_code
        )
        {
            
            $imgFolderClub = $this->getImgFolderClubs();
            $imgFolderFlags = $this->getImgFolderFlags();

            $query ="
            SELECT 
            teams.id AS team_id,
            clubs.id AS club_id,
            IFNULL(clases.name,clubs.name) AS team_name,            
            clubs.name AS club_name,            
            IF( ISNULL(clubs.logo), null,CONCAT('$imgFolderClub', clubs.logo)) AS logo, 
            categories.name AS category_name,
            divisions.name AS division_name,
            CONCAT(countries.name) AS country_name,
            CONCAT('$imgFolderFlags',countries.country_code,'.svg') AS country_flag,
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

            $order = 'club_name';

            if($order_field != null){
                switch ($order_field) {
                
                    case 'club_name':
                        $order ="clubs.name";
                        break;
                    case 'squad':
                        $order ="squad";
                        break;
                    case 'divison_name':
                        $order ="divison_name";
                        break;
                    case 'category_name':
                        $order ="category_name";
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


        public function add(
            $club_id,             
            $category_id,
            $division_id,           
            $team_name,
            $img_team = null
        )
        {
            $db = parent::getDataBase();            

            $query="INSERT INTO $db.teams(club_id, category_id, division_id, team_name, img_team)
            VALUES ($club_id, $category_id, $division_id, '$team_name'";
            
            if(isset($img_team)){
                $query.= ", '$img_team')";                
            }else{
                $query.= ", null)";
            }            

            $verifica = parent::nonQuery($query);
 
            return ($verifica)? 1 : 0;           

        }


        public function delete($team_id)
        {
            $db = parent::getDataBase();            

            $query="DELETE FROM $db.teams WHERE id=$team_id";                   

            $affected = parent::nonQuery($query);
 
            return $affected;           

        }


        public function update(
            $team_id,
            $club_id,             
            $category_id,
            $division_id,           
            $team_name,
            $img_team = null
        )
        {
            $db = parent::getDataBase();            

            $query="UPDATE $db.teams SET
            club_id=$club_id,
            category_id=$category_id, 
            division_id=$division_id, 
            team_name='$team_name'";            
            
            if(isset($img_team)){
                $query.= ", img_team='$img_team' ";                
            }            

            $query.=" WHERE id=$team_id";

            $affected = parent::nonQuery($query);
 
            return $affected;           

        }


        public function getNumberOfMatchesPlayedByTeam($team_id)
        {
            $db = parent::getDataBase(); 

            $query = "SELECT count(*) as matches
            FROM $db.matches
            where team_id_visitor = $team_id 
            or team_id_home = $team_id" ;        

            $rows = parent::obtenerDatos($query);           

            return $rows;
        }


        public function getNumberOfPlayersInTheTeam($team_id)
        {
            $db = parent::getDataBase(); 

            $query = "SELECT count(*) 
            FROM $db.players
            where team_id = $team_id" ;        

            $rows = parent::obtenerDatos($query);           

            return $rows;
        }


        public function deleteTeamPlayers($team_id)
        {
            $db = parent::getDataBase();            

            $query="DELETE FROM $db.players WHERE team_id=$team_id";                   

            $affected = parent::nonQuery($query);
 
            return $affected;
        }


        public function deleteAllPlayersNacionalitiesFromTeam($team_id)
        {
            $db = parent::getDataBase();            

            $query="DELETE nacionalities
            FROM  $db.players_nacionalities nacionalities
            INNER JOIN $db.players players 
            ON players.id = nacionalities.player_id and players.team_id=$team_id";                   

            $affected = parent::nonQuery($query);
 
            return $affected;     
        }


        public function deleteAllPlayersInjuriesFromTeam($team_id)
        {
            $db = parent::getDataBase();            

            $query="DELETE injuries
            FROM  $db.players_injuries injuries
            INNER JOIN $db.players players 
            ON players.id = injuries.player_id and players.team_id=$team_id";                   

            $affected = parent::nonQuery($query);
 
            return $affected;     
        }


        public function deleteAllPlayersSocialMediaFromTeam($team_id)
        {
            $db = parent::getDataBase();            

            $query="DELETE social_media
            FROM  $db.player_social_media social_media
            INNER JOIN $db.players players 
            ON players.id = social_media.player_id and players.team_id=$team_id";                   

            $affected = parent::nonQuery($query);
 
            return $affected;     
        }


        public function deleteAllPlayersMapPositionSecondaryFromTeam($team_id)
        {
            $db = parent::getDataBase();            

            $query="DELETE map_position
            FROM  $db.player_map_position_secondary map_position
            INNER JOIN $db.players players 
            ON players.id = map_position.player_id and players.team_id=$team_id";                   

            $affected = parent::nonQuery($query);
 
            return $affected;     
        }






    }