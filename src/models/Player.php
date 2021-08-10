<?php

    namespace Elitelib;     


    class Player extends Connect{

        private $table = "players";  
        private $path_flag ="imgs/svg/"; 
        private $path ="imgs/";
        private $folder_profile ="players_profile/";  
        private $folder_header ="players_header/"; 
        private $folder_club ="clubs_logo/"; 



        public function getPlayerActionsByAction_id($player_id, $action_id){

            $db = parent::getDataBase();            

            $query = "
            SELECT            
            match_actions.match_date,            
            clubsHome.name AS club_home_name,
            clubsVisitor.name AS club_visitor_name,          
            match_actions.minute,
            match_actions.url_video
            FROM $db.match_actions

            LEFT JOIN $db.matches 
            ON matches.id = match_actions.match_id  

            LEFT JOIN $db.clubs clubsHome
            ON clubsHome.id = matches.club_id_home 

            LEFT JOIN $db.clubs clubsVisitor 
            ON clubsVisitor.id = matches.club_id_visitor 

            WHERE match_actions.player_id=$player_id
            AND match_actions.action_id=$action_id
            ORDER by match_actions.match_date DESC" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }        


        public function getTeamPlayersInfoAndStaticsByPosition(
            $club_id, 
            $team_id, 
            $season_id,
            $language_code,
            $position_id,
            $order,
            $find                     
            ){
            
            $db = parent::getDataBase();
                       
            $actionList = $this->getActionIdList($position_id);
            $actionName = $this->getActionListName($position_id);

            $orderBy = (isset($order))? $order : "players.name";
            
            if( isset($find) && !empty($find)){
                $findScape = parent::scapeParameter($find);                
                $findOut = " and ( LOWER(players.name) like LOWER('%$findScape%') OR LOWER(players.surname) like LOWER('%$findScape%') )";
            }else{
                $findOut = '';
            }


            $query = "
            SELECT 
            players.id,
            players.name, 
            players.surname, 
            players.birthdate,             
            IF( ISNULL(players.img_profile), null,CONCAT('$this->path', '$this->folder_profile', players.img_profile)) AS img_profile_url,
            positions.name as position,
            nacionalities.nacionalities_names,
            nacionalities.nacionalities_flags,
            COALESCE(matches_played, 0) AS matches_played,
            if( COALESCE(matches_played, 0) = 0, '-', COALESCE(action1, 0) ) AS $actionName[0],
            if( COALESCE(matches_played, 0) = 0, '-', COALESCE(action2, 0) ) AS $actionName[1], 
            if( COALESCE(matches_played, 0) = 0, '-', COALESCE(action3, 0) ) AS $actionName[2],
            if( COALESCE(matches_played, 0) = 0, '-', COALESCE(yellow_card, 0) ) AS yellow_card,
            if( COALESCE(matches_played, 0) = 0, '-', COALESCE(red_card, 0) ) AS red_card, 
            IFNULL(injury_img,'ok') AS health_status,
            CASE WHEN injury_img IS NOT NULL 
                THEN injury_description
                ELSE null
            END AS health_detail 

            FROM $db.players players   
            
            LEFT OUTER JOIN $db.map_position_translate positions 
            ON positions.code = players.map_position and positions.translate_code='$language_code'           

            LEFT JOIN (
                SELECT 
                player_nacionality.player_id AS player_id, 
                GROUP_CONCAT(countries.name) AS nacionalities_names,
                GROUP_CONCAT('$this->path_flag',countries.country_code,'.svg') AS nacionalities_flags
                FROM $db.players_nacionalities player_nacionality
                LEFT JOIN $db.country_codes countries
                ON countries.country_code = player_nacionality.country_code
                GROUP BY player_nacionality.player_id
            ) nacionalities ON nacionalities.player_id = players.id

            LEFT JOIN (
                SELECT 
                players_injuries.player_id AS player_id, 
                IFNULL(injuries.name,'N/D') AS injury_description,
                players_injuries.begin AS injury_begin,
                players_injuries.posible_end AS injury_posible_end,
                CONCAT('$this->path_flag','redcross.png') AS injury_img
                FROM $db.players_injuries players_injuries 
                LEFT JOIN $db.injuries_translate injuries 
                ON injuries.injury_id = players_injuries.injury_id and injuries.translate_code='$language_code'
                WHERE players_injuries.season_id=$season_id and isnull(players_injuries.end)
            ) injuries ON injuries.player_id = players.id
            
            LEFT JOIN (
                SELECT match_actions.player_id, 
                COUNT(DISTINCT match_actions.match_id) AS matches_played
                FROM $db.match_Actions match_actions 
                LEFT JOIN  $db.matches matches ON matches.id = match_actions.match_id    
                WHERE matches.season_id=$season_id            
                GROUP BY match_actions.player_id
            ) matches_played_counts ON matches_played_counts.player_id = players.id

            LEFT JOIN (
                SELECT 
                match_actions.player_id as player_id, 
                COUNT(*) AS action1
                FROM $db.match_Actions match_actions
                LEFT JOIN  $db.matches matches ON matches.id = match_actions.match_id
                WHERE match_actions.action_id=$actionList[0] and matches.season_id=$season_id
                GROUP BY match_actions.player_id
            ) action1_counts ON action1_counts.player_id = players.id

            LEFT JOIN (
                SELECT 
                match_actions.player_id as player_id, 
                COUNT(*) AS action2
                FROM $db.match_Actions match_actions
                LEFT JOIN  $db.matches matches ON matches.id = match_actions.match_id
                WHERE match_actions.action_id=$actionList[1] and matches.season_id=$season_id
                GROUP BY match_actions.player_id
            ) action2_counts ON action2_counts.player_id = players.id

            LEFT JOIN (
                SELECT 
                match_actions.player_id as player_id, 
                COUNT(*) AS action3
                FROM $db.match_Actions match_actions
                LEFT JOIN  $db.matches matches ON matches.id = match_actions.match_id
                WHERE match_actions.action_id=$actionList[2] and matches.season_id=$season_id
                GROUP BY match_actions.player_id
            ) action3_counts ON action3_counts.player_id = players.id

            LEFT JOIN (
                SELECT 
                match_actions.player_id, 
                COUNT(*) AS yellow_card
                FROM $db.match_Actions match_actions
                LEFT JOIN  $db.matches matches ON matches.id = match_actions.match_id
                WHERE match_actions.action_id=$actionList[3] and matches.season_id=$season_id
                GROUP BY match_actions.player_id 
            ) yellow_card_counts ON yellow_card_counts.player_id = players.id

            LEFT JOIN (
                SELECT 
                match_actions.player_id, 
                COUNT(*) AS red_card
                FROM $db.match_Actions match_actions
                LEFT JOIN  $db.matches matches ON matches.id = match_actions.match_id
                WHERE match_actions.action_id=$actionList[4] and matches.season_id=$season_id
                GROUP BY match_actions.player_id                
            ) red_card_counts ON red_card_counts.player_id = players.id

            WHERE players.club_id=$club_id 
            and players.team_id=$team_id
            and players.position_id=$position_id
            $findOut
            ORDER BY $orderBy" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }  
        
        public function getTeamPlayersInfoByPosition(
            $club_id, 
            $team_id,             
            $language_code,
            $position_id,
            $order,
            $find                     
            ){
            
            $db = parent::getDataBase();                      
            
            $actionName = $this->getActionListName($position_id);

            $orderBy = (isset($order))? $order : "players.name";
            
            if( isset($find) && !empty($find)){
                $findScape = parent::scapeParameter($find);                
                $findOut = " and ( LOWER(players.name) like LOWER('%$findScape%') OR LOWER(players.surname) like LOWER('%$findScape%') )";
            }else{
                $findOut = '';
            }

            $query = "
            SELECT 
            players.id,
            players.name, 
            players.surname, 
            players.birthdate,             
            IF( ISNULL(players.img_profile), null,CONCAT('$this->path', '$this->folder_profile', players.img_profile)) AS img_profile_url,
            positions.name as position,
            nacionalities.nacionalities_names,
            nacionalities.nacionalities_flags,
            0 AS matches_played,
            '-' AS $actionName[0],
            '-' AS $actionName[1],
            '-' AS $actionName[2],
            '-' AS yellow_card,
            '-' AS red_card,
            'ok' AS health_status,
            null AS health_detail

            FROM $db.players players   
            
            LEFT OUTER JOIN $db.map_position_translate positions 
            ON positions.code = players.map_position and positions.translate_code='$language_code'           

            LEFT JOIN (
                SELECT 
                player_nacionality.player_id AS player_id, 
                GROUP_CONCAT(countries.name) AS nacionalities_names,
                GROUP_CONCAT('$this->path_flag',countries.country_code,'.svg') AS nacionalities_flags
                FROM $db.players_nacionalities player_nacionality
                LEFT JOIN $db.country_codes countries
                ON countries.country_code = player_nacionality.country_code
                GROUP BY player_nacionality.player_id
            ) nacionalities ON nacionalities.player_id = players.id            

            WHERE players.club_id=$club_id 
            and players.team_id=$team_id
            and players.position_id=$position_id
            $findOut
            ORDER BY $orderBy" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }  

        public function findPlayers($find, $language_code, $page = 1){  

            $db = parent::getDataBase();
            $inicio = 0;
            $cantidad = 100;

            if($page > 1){                
                $inicio = ($cantidad * ($page - 1)) + 1 ;
                $cantidad = $cantidad * $page;
            }

            $findScape = parent::scapeParameter($find);

            $query = "
            SELECT 
            players.id,
            players.name, 
            players.surname, 
            players.birthdate,                         
            IF( ISNULL(players.img_profile), null,CONCAT('$this->path', '$this->folder_profile', players.img_profile)) AS img_profile_url,
            clubs.name AS club_name,
            positions.name as position,
            nacionalities.nacionalities_names,
            nacionalities.nacionalities_flags
            FROM $db.players players            
            LEFT OUTER JOIN $db.map_position_translate positions 
            ON positions.code = players.map_position and positions.translate_code='$language_code'

            LEFT JOIN $db.clubs clubs ON clubs.id = players.club_id         

            LEFT JOIN (
                SELECT 
                player_nacionality.player_id AS player_id, 
                GROUP_CONCAT(countries.name) AS nacionalities_names,
                GROUP_CONCAT('$this->path_flag',countries.country_code,'.svg') AS nacionalities_flags
                FROM $db.players_nacionalities player_nacionality
                LEFT JOIN $db.country_codes countries
                ON countries.country_code = player_nacionality.country_code
                GROUP BY player_nacionality.player_id
            ) nacionalities ON nacionalities.player_id = players.id

            WHERE (LOWER(players.name) like LOWER('%$findScape%')) OR (LOWER(players.surname) like LOWER('%$findScape%')) 
            limit $inicio,$cantidad" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }    
        
        
        public function findPlayersFast($find, $language_code, $limit = 10){  

            $db = parent::getDataBase();              

            $findScape = parent::scapeParameter($find);

            $query = "
            SELECT 
            players.id,
            players.name, 
            players.surname,  
            positions.name AS position_name,   
            clubs.name AS club_name,             
            IF( ISNULL(players.img_profile), null,CONCAT('$this->path', '$this->folder_profile', players.img_profile)) AS img_profile_url
            FROM $db.players players 
            LEFT OUTER JOIN $db.map_position_translate positions 
            ON positions.code = players.map_position and positions.translate_code='$language_code'
            
            LEFT JOIN $db.clubs clubs ON clubs.id = players.club_id
            
            WHERE (LOWER(players.name) like LOWER('%$findScape%')) OR (LOWER(players.surname) like LOWER('%$findScape%')) 
            limit $limit" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }  

        


        public function getTeamPlayersInfo($club_id, $team_id, $language_code){
            
            $db = parent::getDataBase(); 

            $query = "
            SELECT 
            p.id, 
            p.name, 
            p.surname, 
            p.birthdate,            
            IF( ISNULL(p.img_profile), null,CONCAT('$this->path', '$this->folder_profile', p.img_profile)) AS img_profile_url,
            p.nationality_id,
            p.nationality2_id,
            ps.name As position,
            pst.name As position_name 
            FROM $db.players p            
            LEFT OUTER JOIN $db.positions ps ON p.position_id = ps.id 
            LEFT OUTER JOIN $db.position_translate pst ON p.position_id = pst.id and pst.country_code='$language_code' 
            WHERE p.club_id=$club_id and p.team_id=$team_id" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        } 



        public function getPlayerPerfil($player_id, $language_id){

            $db = parent::getDataBase(); 

            $query = "
            SELECT
            pl.id AS player_id,
            pl.name,
            pl.surname,
            pl.height,
            pl.weight,
            pl.birthdate,            
            IF( ISNULL(pl.img_profile), null,CONCAT('$this->path', '$this->folder_profile', pl.img_profile)) AS img_profile_url,
            IF( ISNULL(pl.img_header), null,CONCAT('$this->path', '$this->folder_header', pl.img_header)) AS img_header_url,             
            pl.jersey_nro,
            clubs.name AS 'club_name',            
            IF( ISNULL(clubs.logo), null,CONCAT('$this->path','$this->folder_club', clubs.logo)) AS logo,
            GROUP_CONCAT(cc.name) AS 'nacionalities_names',
            GROUP_CONCAT('$this->path_flag',pn.country_code,'.svg') AS 'nacionalities_flags',
            ofi.name AS 'outfitter_name',
            ft.name AS 'main_foot',      
            pt.name AS 'name_main_position',
            pl.map_position AS map_main_position
            FROM players pl
            LEFT JOIN clubs clubs ON
                pl.club_id = clubs.id
            INNER JOIN outfitter ofi ON
                pl.outfitter_id = ofi.id
            INNER JOIN players_nacionalities pn ON
                pl.id = pn.player_id
            INNER JOIN country_codes cc ON
                pn.country_code=cc.country_code
            INNER JOIN foot_translate ft ON
                pl.foot_code = ft.foot_code
            LEFT JOIN positions p ON
                pl.position_id=p.id
            INNER JOIN position_translate pt ON
                p.id = pt.ID
            WHERE
                pl.id =$player_id AND ft.country_code = '$language_id' 
                AND pt.country_code='$language_id'" ;        

            $datos = parent::obtenerDatos($query);    

            return $datos;
        }


        public function getSecondaryPositions($player_id, $language_id){

            $db = parent::getDataBase(); 

            $query = "
            SELECT 
            s.position_code as position_code,
            t.name as description
            FROM $db.player_map_position_secondary s
            LEFT OUTER JOIN $db.map_position_translate t 
            ON t.code = s.position_code and t.translate_code='$language_id'
            WHERE player_id=$player_id" ;        

            $datos = parent::obtenerDatos($query);    

            return $datos;
        }


        public function getInjuriesHistory($player_id, $language_id){

            $db = parent::getDataBase(); 

            $query = "
            SELECT
            history.begin AS date_begin,
            history.end AS date_end,
            history.posible_end AS posible_date_end,
            injuries.name AS injury_name
            FROM $db.players_injuries history
            LEFT JOIN $db.injuries_translate injuries ON
            injuries.injury_id = history.injury_id and injuries.translate_code='$language_id'
            WHERE history.player_id=$player_id
            ORDER BY history.begin DESC" ;        

            $datos = parent::obtenerDatos($query);    

            return $datos;
        }


        
        
        public function getPosition_id($player_id){

            $db = parent::getDataBase(); 

            $query = "
            SELECT 
            position_id           
            FROM $db.players 
            WHERE id=$player_id" ;        

            $datos = parent::obtenerDatos($query);    

            return $datos[0]['position_id'];
        }


        private function getActionIdList($category_id){          

            switch ($category_id) {
                case 1://goalkeeper
                    return array('34', '30','31', '32', '33'); 

                case 2://defense
                    return array('10','1', '4', '32', '33');         

                case 3://midlefield
                    return array('10','11', '23', '32', '33');  

                case 4://attack
                    return array('10','11', '13', '32', '33');                    

                default://attack
                    return array('10','11', '13', '32', '33');
                   
            }
            
        }      


        public function getCategoriesByLanguage($language_id){          

           
           $db = parent::getDataBase(); 

            $query = "
            SELECT
            id, 
            name 
            FROM $db.position_translate
            where country_code='$language_id'";        

            $datos = parent::obtenerDatos($query);           
 
            return $datos;
            
        }     



        public function getActionListName($category_id){          

            switch ($category_id) {

                case 1:
                    return array('goals_received', 'saves','shots_received'); 
                case 2:
                    return array('goals','tackles_success', 'fouls');                   
                case 3:
                    return array('goals','shots_success', 'Passes_success');                   
                case 4:
                    return array('goals','shots_success', 'dribbling_success');                   

                default:
                    return array('goals','shots_success', 'dribbling_success');
                   
            }
            
        }


        public function getActionNameList($actionIdList, $language_id){

            $db = parent::getDataBase(); 

            $actions_id = implode(",",$actionIdList );

            $query = "
            SELECT 
            name 
            FROM $db.actions
            WHERE id in ($actions_id)";        

            $datos = parent::obtenerDatos($query);           
 
            return $datos;

        }
       



    }