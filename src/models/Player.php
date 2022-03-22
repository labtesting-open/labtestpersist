<?php

    namespace Elitelib;     


    class Player extends Connect{       


        public function getPlayerActionsByAction_id($player_id, $action_id){

            $db = parent::getDataBase();
            
            $imgFolderClub = $this->getImgFolderClubs();

            $query = "
            SELECT
            match_actions.id AS match_action_id,            
            match_actions.match_date,            
            clubsHome.name AS club_home_name,            
            clubsVisitor.name AS club_visitor_name,
            matches.goals_home_team,
            matches.goals_visitor_team,
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
        
        
        public function getTeamPlayersInfoAndStaticsByPositionV2(
            $club_id, 
            $team_id, 
            $season_id,            
            $position_id,
            $language_code = null,
            $order = null,
            $order_sense = null,
            $find = null,
            $user_id = null                     
            ){
            
            $db = parent::getDataBase();
            $imgFolderPlayersProfile = $this->getImgFolderPlayerProfiles();
            $imgFolderFlags = $this->getImgFolderFlags();
                       
            $actionList = $this->getActionIdList($position_id);
            $actionName = $this->getActionListName($position_id);

            $language_code = (isset($language_code) && $language_code != null)? $language_code: 'GB';            

            switch ($order) {
                
                case 'player_name':
                    $orderfields ="fullname";
                    break;
                case 'player_age':
                    $orderfields ="player_age";
                    break;
                case 'matches_played':
                        $orderfields ="matches_played";
                        break;
                default:
                    $orderfields ="fullname";
            }            

            $sense = ( $order_sense != null && $order_sense =='ASC')?" ASC ":" DESC ";
            
            if( isset($find) && !empty($find)){
                $findScape = parent::scapeParameter($find);                
                $findOut = " and ( LOWER(players.name) like LOWER('%$findScape%') OR LOWER(players.surname) like LOWER('%$findScape%') )";
            }else{
                $findOut = '';
            }

            $own_favourite_field ='';
            $own_favourite_join ='';

            if($user_id != null && is_numeric($user_id)){

                $own_favourite_field = ",IF( ISNULL(user_id_mark), 'false', 'true') AS own_favourite ";
                $own_favourite_join = "
                LEFT JOIN (
                    SELECT
                    player_id
                    ,date_added
                    ,date_news_checked 
                    ,user_id AS user_id_mark 
                    FROM $db.users_favorites_players
                    where user_id = $user_id
                ) AS favorites ON favorites.player_id = players.id";

            }

            $query = "
            SELECT
            players.id AS player_id, 
            players.name AS player_name,
            players.surname AS player_surname,
            CONCAT(players.name, ' ',players.surname) AS fullname,
            TIMESTAMPDIFF(YEAR,players.birthdate,CURDATE()) AS player_age,            
            IF( ISNULL(players.img_profile), null,CONCAT('$imgFolderPlayersProfile', players.img_profile)) AS img_profile_url,
            positions.name as position,
            colorposition.color_hexa,
            nacionalities.nacionalities_names,
            nacionalities.nacionalities_flags,
            COALESCE(matches_played, 0) AS matches_played,
            if( COALESCE(matches_played, 0) = 0, '-', COALESCE(avg_minutes, 0) ) AS avg_minutes, 
            if( COALESCE(matches_played, 0) = 0, '-', COALESCE(to_start, 0) ) AS to_start,
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
            $own_favourite_field

            FROM $db.players players   
            
            LEFT OUTER JOIN $db.map_position_translate positions 
            ON positions.code = players.map_position and positions.translate_code='$language_code'
            
            LEFT OUTER JOIN elites17_wizard.positions colorposition
            ON colorposition.id = players.position_id

            LEFT JOIN (
                SELECT 
                player_nacionality.player_id AS player_id, 
                GROUP_CONCAT(countries.name) AS nacionalities_names,
                GROUP_CONCAT('$imgFolderFlags',countries.country_code,'.svg') AS nacionalities_flags
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
                CONCAT('$imgFolderFlags','redcross.png') AS injury_img
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

            LEFT JOIN (            
				SELECT
				minutes.player_id AS player_id,
				SUM(minutes.to_start) AS to_start,
				ROUND(AVG(minutes.minutes)) AS avg_minutes
				FROM $db.match_player_minutes minutes
				INNER JOIN $db.matches matches
				ON matches.id =  minutes.match_id
				where matches.season_id = $season_id
				GROUP BY minutes.player_id
            ) match_time ON match_time.player_id = players.id

            $own_favourite_join

            WHERE players.club_id=$club_id 
            and players.team_id=$team_id
            and players.position_id=$position_id
            $findOut
            ORDER BY $orderfields $sense" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }


        public function getTeamPlayersInfoAndStaticsByPosition(
            $club_id, 
            $team_id, 
            $season_id,
            $language_code = null,
            $position_id,
            $order,
            $find                     
            ){
            
            $db = parent::getDataBase();
            $imgFolderPlayersProfile = $this->getImgFolderPlayerProfiles();
            $imgFolderFlags = $this->getImgFolderFlags();
                       
            $actionList = $this->getActionIdList($position_id);
            $actionName = $this->getActionListName($position_id);

            $language_code = (isset($language_code) && $language_code != null)? $language_code: 'GB';

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
            TIMESTAMPDIFF(YEAR,players.birthdate,CURDATE()) AS age,             
            IF( ISNULL(players.img_profile), null,CONCAT('$imgFolderPlayersProfile', players.img_profile)) AS img_profile_url,
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
                GROUP_CONCAT('$imgFolderFlags',countries.country_code,'.svg') AS nacionalities_flags
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
                CONCAT('$imgFolderFlags','redcross.png') AS injury_img
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

            $imgFolderPlayersProfile = $this->getImgFolderPlayerProfiles();
            $imgFolderFlags = $this->getImgFolderFlags();

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
            IF( ISNULL(players.img_profile), null,CONCAT('$imgFolderPlayersProfile', players.img_profile)) AS img_profile_url,
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
                GROUP_CONCAT('$imgFolderFlags',countries.country_code,'.svg') AS nacionalities_flags
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

        public function findPlayers($find, $language_code, $page = 1, $limit= 100){  

            $db = parent::getDataBase();
            
            $imgFolderFlags = $this->getImgFolderFlags();
            $imgFolderPlayersProfile = $this->getImgFolderPlayerProfiles();

            $init = 0;            

            if($page > 1){                
                $init = ($limit * ($page - 1)) + 1 ;
                $limit = $limit * $page;
            }

            $findScape = parent::scapeParameter($find);

            $query = "
            SELECT 
            players.id,
            players.name, 
            players.surname, 
            players.birthdate,                         
            IF( ISNULL(players.img_profile), null,CONCAT('$imgFolderPlayersProfile', players.img_profile)) AS img_profile_url,
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
                GROUP_CONCAT('$imgFolderFlags',countries.country_code,'.svg') AS nacionalities_flags
                FROM $db.players_nacionalities player_nacionality
                LEFT JOIN $db.country_codes countries
                ON countries.country_code = player_nacionality.country_code
                GROUP BY player_nacionality.player_id
            ) nacionalities ON nacionalities.player_id = players.id

            WHERE (LOWER(players.name) like LOWER('%$findScape%')) OR (LOWER(players.surname) like LOWER('%$findScape%')) 
            limit $init,$limit" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }    
        
        
        public function findPlayersFast($find, $language_code, $limit = 10){  

            $db = parent::getDataBase();              
            $imgFolderPlayersProfile = $this->getImgFolderPlayerProfiles();

            $findScape = parent::scapeParameter($find);

            $query = "
            SELECT 
            players.id,
            players.name, 
            players.surname,  
            positions.name AS position_name,   
            clubs.name AS club_name,             
            IF( ISNULL(players.img_profile), null,CONCAT('$imgFolderPlayersProfile', players.img_profile)) AS img_profile_url
            FROM $db.players players 
            LEFT OUTER JOIN $db.map_position_translate positions 
            ON positions.code = players.map_position and positions.translate_code='$language_code'
            
            LEFT JOIN $db.clubs clubs ON clubs.id = players.club_id
            
            WHERE (LOWER(players.name) like LOWER('%$findScape%')) OR (LOWER(players.surname) like LOWER('%$findScape%')) 
            limit $limit" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }


        public function searchQuick(
            $find = null, 
            $limit = null, 
            $language_code = null, 
            $order = null, 
            $order_sense = null,
            $user_id = null
            ){  

            if(is_null($find) || empty($find)) return array();

            $db = parent::getDataBase();

            $imgFolderPlayersProfile = $this->getImgFolderPlayerProfiles();
            $imgFolderFlags = $this->getImgFolderFlags();
            $imgFolderClub = $this->getImgFolderClubs();

            $findScape = parent::scapeParameter($find);

            $language_code = (isset($language_code) && $language_code != null)? $language_code: 'GB';
            
            $limitReg = (isset($limit))? $limit : 10;

            switch ($order) {
                
                case 'name':
                    $orderfields ="player_fullname";
                    break;
                case 'player_name':
                        $orderfields ="player_fullname";
                        break;                
                default:
                    $orderfields ="player_fullname";
            }            

            $sense = ( $order_sense != null && $order_sense =='ASC')?" ASC ":" DESC ";

            $own_favourite_field ='';
            $own_favourite_join ='';

            if($user_id != null && is_numeric($user_id)){

                $own_favourite_field = ",IF( ISNULL(user_id_mark), 'false', 'true') AS own_favourite ";
                $own_favourite_join = "
                LEFT JOIN (
                    SELECT
                    player_id
                    ,date_added
                    ,date_news_checked 
                    ,user_id AS user_id_mark 
                    FROM $db.users_favorites_players
                    where user_id = $user_id
                ) AS favorites ON favorites.player_id = players.id";

            }

            $query = "
            SELECT
            players.id AS player_id,             
            CONCAT(players.name, ' ', players.surname) AS player_fullname,
            players.name AS player_name, 
            players.surname AS player_surname,            
            IF( ISNULL(players.img_profile), null,CONCAT('$imgFolderPlayersProfile', players.img_profile)) AS img_profile_url,
            clubs.name AS club_name,
            IF( ISNULL(clubs.logo), null,CONCAT('$imgFolderClub', clubs.logo)) AS club_logo,
            positions.name as map_position_name,
            colorposition.color_hexa,
            nacionalities.nacionalities_names,
            nacionalities.nacionalities_flags
            $own_favourite_field

            FROM $db.players players

            LEFT OUTER JOIN $db.map_position_translate positions 
            ON positions.code = players.map_position and positions.translate_code='$language_code'

            LEFT JOIN $db.clubs clubs ON clubs.id = players.club_id
            
            LEFT OUTER JOIN $db.positions colorposition
            ON colorposition.id = players.position_id

            LEFT JOIN (
                SELECT 
                player_nacionality.player_id AS player_id, 
                GROUP_CONCAT(countries.name) AS nacionalities_names,
                GROUP_CONCAT('$imgFolderFlags',countries.country_code,'.svg') AS nacionalities_flags
                FROM $db.players_nacionalities player_nacionality
                LEFT JOIN $db.country_codes countries
                ON countries.country_code = player_nacionality.country_code
                GROUP BY player_nacionality.player_id
            ) nacionalities ON nacionalities.player_id = players.id

            $own_favourite_join
            
            WHERE 
            LOWER( CONCAT(players.name, ' ', players.surname)) like LOWER('%$findScape%')  
            ORDER BY $orderfields $sense    
            limit $limitReg" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }

        


        public function getTeamPlayersInfo($club_id, $team_id, $language_code){
            
            $db = parent::getDataBase();
            $imgFolderPlayersProfile = $this->getImgFolderPlayerProfiles();

            $query = "
            SELECT 
            p.id, 
            p.name, 
            p.surname, 
            p.birthdate,            
            IF( ISNULL(p.img_profile), null,CONCAT('$imgFolderPlayersProfile', p.img_profile)) AS img_profile_url,
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



        public function getPlayerPerfil($player_id, $language_code = null, $user_id = null){

            $db = parent::getDataBase(); 

            $imgFolderPlayersProfile = $this->getImgFolderPlayerProfiles();           
            $imgFolderFlags = $this->getImgFolderFlags();
            $imgFolderClub = $this->getImgFolderClubs();

            $language_code = (isset($language_code) && $language_code != null)? $language_code: 'GB';

            $own_favourite_field ='';
            $own_favourite_join ='';

            if($user_id != null && is_numeric($user_id)){

                $own_favourite_field = ",IF( ISNULL(user_id_mark), 'false', 'true') AS own_favourite ";
                $own_favourite_join = "
                LEFT JOIN (
                    SELECT
                    player_id
                    ,date_added
                    ,date_news_checked 
                    ,user_id AS user_id_mark 
                    FROM $db.users_favorites_players
                    where user_id = $user_id
                ) AS favorites ON favorites.player_id = pl.id";

            }

            $query = "
            SELECT
            pl.id AS player_id,
            pl.name,
            pl.surname,
            pl.height,
            pl.weight,            
            TIMESTAMPDIFF(YEAR,pl.birthdate,CURDATE()) AS player_age,            
            IF( ISNULL(pl.img_profile), null,CONCAT('$imgFolderPlayersProfile', pl.img_profile)) AS img_profile_url,                         
            pl.jersey_nro,
            clubs.name AS 'club_name',            
            IF( ISNULL(clubs.logo), null,CONCAT('$imgFolderClub', clubs.logo)) AS club_logo,
            GROUP_CONCAT(cc.name) AS 'nationality_name',
            GROUP_CONCAT('$imgFolderFlags',pn.country_code,'.svg') AS 'nationality_flag',
            ofi.name AS 'outfitter_name',            
            CASE
                WHEN pl.foot_code = 0 THEN 'L'
                WHEN pl.foot_code = 1 THEN 'R'
                WHEN pl.foot_code = 2 THEN 'B'
                ELSE 'R'
            END AS foot,
            ft.name AS 'main_foot',
            pl.map_position AS map_main_position,     
            map_position_translate.name AS map_main_position_name,
            colorposition.color_hexa
            $own_favourite_field

            FROM $db.players pl
            LEFT JOIN $db.clubs clubs ON
                pl.club_id = clubs.id
            INNER JOIN $db.outfitter ofi ON
                pl.outfitter_id = ofi.id
            INNER JOIN $db.players_nacionalities pn ON
                pl.id = pn.player_id
            INNER JOIN $db.country_codes cc ON
                pn.country_code=cc.country_code
            INNER JOIN $db.foot_translate ft ON
                pl.foot_code = ft.foot_code
            LEFT JOIN $db.positions p ON
                pl.position_id=p.id
            LEFT JOIN $db.map_position_translate map_position_translate ON
                map_position_translate.code = pl.map_position and map_position_translate.translate_code='$language_code'
            LEFT OUTER JOIN $db.positions colorposition
                ON colorposition.id = pl.position_id
            $own_favourite_join    
            WHERE
                pl.id =$player_id AND ft.country_code = '$language_code'" ;        

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


        public function getActionIdList($category_id){          

            switch ($category_id) {
                case 1://goalkeeper
                    return array('10', '30','35', '32', '33'); 

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
                    return array('goals', 'saves','penalties_saved'); 
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


        public function getAvailableNationalities(
            $continent_code=null, 
            $country_code = null, 
            $category_id = null,
            $division_id = null,
            $club_id = null,
            $nacionality_code_list = null, 
            $orderField = null,
            $orderSense = null
        )
        {
            $db = parent::getDataBase();

            $imgFolderFlags = $this->getImgFolderFlags();   
            
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
            
            $selected = '';

            if ($nacionality_code_list != null) {
                $selected =",IF(nacionalities.country_code in ($nacionality_code_list),'true', 'false') AS selected ";
            }

            $order = 'nacionalities.country_name';

            if($orderField != null){
                $order = $orderField;
            }
            
            $sense = (isset($orderSense) && $orderSense !='ASC')?'DESC':'ASC';


            $query = "
            SELECT  
            nacionalities.country_code,
            nacionalities.country_name,
            CONCAT('$imgFolderFlags',nacionalities.country_code,'.svg') AS country_flags
            $selected
            FROM  $db.players players
            INNER JOIN (
                SELECT
                clubs.id,
                clubs.name,
                teams.category_id,
                teams.division_id,
                countries.name AS country_name     
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
            country_codes.name as country_name
            FROM $db.players_nacionalities nacionalities 
            LEFT JOIN $db.country_codes country_codes 
            ON country_codes.country_code = nacionalities.country_code
            ) AS nacionalities ON nacionalities.player_id = players.id
            
            GROUP BY nacionalities.country_code
            ORDER BY $order $sense";        

            $datos = parent::obtenerDatos($query);           
 
            return $datos;
        }


        public function getAllPrimaryPositions( 
            $orderField = null,
            $orderSense = null,
            $language_code = null 
        )
        {
            $db = parent::getDataBase();           

            $language = (isset($language_code) && $language_code != null)? $language_code: 'GB';
           
            $order = 'positions.id';

            if($orderField != null){
                $order = $orderField;
            }
            
            $sense = (isset($orderSense) && $orderSense !='ASC')?'DESC':'ASC';

            $query = "
            SELECT
            positions.id AS position_id,
            position_translate.name AS position_name
            FROM $db.positions positions
            LEFT JOIN $db.position_translate 
            ON position_translate.id = positions.id AND position_translate.country_code = '$language'                        
            ORDER BY $order $sense";        

            $datos = parent::obtenerDatos($query);           
 
            return $datos;
        }


        public function getAllSecondaryPositions(
            $orderField = null,
            $orderSense = null,
            $language_code = null 
        )
        {
            $db = parent::getDataBase();

            $language = (isset($language_code) && $language_code != null)? $language_code: 'GB';
           
            $order = 'position_translate.name';

            if($orderField != null){
                $order = $orderField;
            }
            
            $sense = (isset($orderSense) && $orderSense !='ASC')?'DESC':'ASC';


            $query = "
            SELECT  
            position_translate.code,
            position_translate.name
            FROM $db.map_position_translate position_translate
            WHERE translate_code='$language'                        
            ORDER BY $order $sense";        

            $datos = parent::obtenerDatos($query);           
 
            return $datos;
        }


        private function getQueryPlayersWithFilters(
            $db,            
            $whereNationality,
            $whereSecondPositions,
            $joinSecondPositions,
            $where,
            $language_code,
            $user_id
        )
        {
            
            $language_code = (isset($language_code))? $language_code: 'GB';

            $imgFolderClub = $this->getImgFolderClubs();
            $imgFolderFlags = $this->getImgFolderFlags();
            $imgFolderPlayersProfile = $this->getImgFolderPlayerProfiles();

            $own_favourite_field ='';
            $own_favourite_join ='';

            if($user_id != null && is_numeric($user_id)){

                $own_favourite_field = ",IF( ISNULL(user_id_mark), 'false', 'true') AS own_favourite ";
                $own_favourite_join = "
                LEFT JOIN (
                    SELECT
                    player_id
                    ,date_added
                    ,date_news_checked 
                    ,user_id AS user_id_mark 
                    FROM $db.users_favorites_players
                    where user_id = $user_id
                ) AS favorites ON favorites.player_id = players.id";

            }

            $query = "
            SELECT  
            players.id AS player_id
            ,players.name AS player_name
            ,players.surname AS player_surname
            ,IF( ISNULL(players.img_profile), null,CONCAT('$imgFolderPlayersProfile', players.img_profile)) AS img_profile_url
            ,TIMESTAMPDIFF(YEAR,players.birthdate,CURDATE()) AS player_age
            ,players.height AS player_height
            ,players.weight AS player_weight            
            ,CASE
                WHEN players.foot_code = 0 THEN 'L'
                WHEN players.foot_code = 1 THEN 'R'
                WHEN players.foot_code = 2 THEN 'B'
                ELSE 'R'
            END AS foot
            ,nacionalities.nationalities_flags AS nationalities_flags
            ,nacionalities.nationalities_names AS nationalities_names
            ,colorposition.color_hexa
            ,positions.id AS position_id
            ,positions.name AS position_name
            ,players.map_position AS map_main_position
            ,map_position_translate.name AS map_main_position_name
            ,second_position.second_positions_codes AS second_positions_codes
            ,second_position.second_positions_names AS second_positions_names 
			,players.club_id
            ,clubs.name AS club_name
            ,IF( ISNULL(clubs.logo), null,CONCAT('$imgFolderClub', clubs.logo)) AS logo
            ,players.team_id
            ,teams.team_name AS team_name
            ,countries.name AS country_name
			,teams.division_id
            ,divisions.name AS division_name
            ,divisions.division_class_id AS division_class_id
            ,teams.category_id AS category_id
            ,IFNULL(injuries.injury_description,'ok') AS health_status
            $own_favourite_field

            FROM  $db.players players

            INNER JOIN $db.clubs clubs 
                ON clubs.id = players.club_id

            LEFT JOIN $db.teams teams  
                ON teams.id = players.team_id

            INNER JOIN $db.country_codes countries 
                ON countries.country_code = clubs.country_code

            LEFT JOIN $db.division divisions 
                ON divisions.id = teams.division_id

            LEFT JOIN $db.map_position_translate map_position_translate 
                ON map_position_translate.code = players.map_position 
                AND map_position_translate.translate_code='$language_code'

            LEFT OUTER JOIN elites17_wizard.positions colorposition
                ON colorposition.id = players.position_id

            $own_favourite_join

            LEFT JOIN(
				SELECT
				players_injuries.player_id AS player_id
				,IFNULL(players_injuries.posible_end,'N/D') AS injury_posible_end
				,IFNULL(injuries_translate.name,'N/D') AS injury_description
				FROM $db.players_injuries players_injuries
				LEFT JOIN $db.injuries_translate injuries_translate 
					ON injuries_translate.injury_id = players_injuries.injury_id 
					and injuries_translate.translate_code='$language_code'
				WHERE ISNULL(players_injuries.end) 
            ) injuries ON injuries.player_id = players.id

            INNER JOIN (
                SELECT 
                player_nacionality.player_id,
                nationalities_full.nationalities_codes,
                nationalities_full.nationalities_flags,
                nationalities_full.nationalities_names  
                FROM $db.players_nacionalities player_nacionality  
                LEFT JOIN(
                  SELECT 
                  nacionalities.player_id,
                  group_concat(nacionalities.country_code) AS nationalities_codes ,
                  group_concat( CONCAT('$imgFolderFlags', nacionalities.country_code,'.svg')) AS nationalities_flags,
                  group_concat( country_codes.name) AS nationalities_names
                  FROM $db.players_nacionalities nacionalities 
                  LEFT JOIN $db.country_codes country_codes
                  ON country_codes.country_code = nacionalities.country_code	
                  GROUP by nacionalities.player_id
                ) AS nationalities_full ON nationalities_full.player_id = player_nacionality.player_id                  
                $whereNationality
            ) AS nacionalities ON nacionalities.player_id = players.id

            LEFT JOIN $db.positions positions ON positions.id = players.position_id            
            $joinSecondPositions JOIN (
                SELECT
				secondary_positions.player_id,
				second_positions_full.map_positions_codes AS second_positions_codes,
				second_positions_full.map_positions_names AS second_positions_names
				FROM $db.player_map_position_secondary secondary_positions  
				LEFT JOIN(
					SELECT
					secondary_positions.player_id,
					group_concat(secondary_positions.position_code) AS map_positions_codes,
					group_concat(map_position_t.name) AS map_positions_names
					FROM $db.player_map_position_secondary secondary_positions
					LEFT JOIN $db.map_position_translate map_position_t
					ON  map_position_t.code = secondary_positions.position_code AND map_position_t.translate_code='$language_code'             
					GROUP BY secondary_positions.player_id
				) AS second_positions_full ON second_positions_full.player_id = secondary_positions.player_id
				$whereSecondPositions
				GROUP BY secondary_positions.player_id
			) AS second_position ON second_position.player_id = players.id
            $where
            GROUP BY players.id"; 

            return $query;

        }

        private function getFiltersPlayersWithFilters(
            $continent_code=null, 
            $country_code = null, 
            $category_id = null,
            $division_id = null,
            $club_id = null,
            $nationality_code = null, 
            $position_id = null,
            $second_positions_codes = null,
            $age_range = null,
            $height_range = null,
            $weight_range = null,
            $foot = null,
            $orderField = null,
            $orderSense = null,
            $page = null,
            $limit = null,
            $language_code = null
        )
        { 

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
                $where.= " teams.category_id = $category_id";
            }           

            if($division_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= " teams.division_id = $division_id";
            }

            if($club_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= " players.club_id = $club_id";
            }  
            
            if($position_id != null){
                $where.=(empty($where))?' WHERE ':' and ';               
                $where.= " positions.id = $position_id ";
            }

            if($foot != null){
                switch ($foot) {
                    case 'L':
                        $footCode = 0; 
                        break;
                    case 'R':
                        $footCode = 1;
                        break;
                    case 'B':
                        $footCode = 2;
                        break;
                    default:
                        $footCode = 1;
                }                
                $where.=(empty($where))?' WHERE ':' and ';                
                $where.= " players.foot_code = $footCode ";
            }  

            if($height_range != null){
                $heightRange = explode(',', $height_range);
                if(count($heightRange) > 1){
                    $where.=(empty($where))?' WHERE ':' and ';                
                    $where.= " players.height BETWEEN $heightRange[0] AND $heightRange[1] ";
                }
            }

            if($weight_range != null){
                $weightRange = explode(',', $weight_range);
                if(count($weightRange) > 1){
                    $where.=(empty($where))?' WHERE ':' and ';                
                    $where.= " players.weight BETWEEN $weightRange[0] AND $weightRange[1] ";
                }
            }

            if($age_range != null){
                $ageRange = explode(',', $age_range);
                if(count($ageRange) > 1){
                    $where.=(empty($where))?' WHERE ':' and ';                
                    $where.= " TIMESTAMPDIFF(YEAR,players.birthdate,CURDATE()) BETWEEN $ageRange[0] AND $ageRange[1] ";
                }
            }

            $whereNationality = "";

            if($nationality_code != null){
                $whereNationality.=' WHERE ';                
                $whereNationality.= " player_nacionality.country_code='$nationality_code'";
            }

            $whereSecondPositions = "";
            $joinSecondPositions ="LEFT";

            if($second_positions_codes != null){
                $secondPositions = "";
                $secondPositionsArray = explode(',', $second_positions_codes);
                foreach($secondPositionsArray as $position => $value){
                    $secondPositions .="'".$value."',";  
                }
                $positionsCodes = substr($secondPositions, 0, -1); 
                $joinSecondPositions ="INNER";
                $whereSecondPositions.=(empty($where))?' WHERE ':' and ';                
                $whereSecondPositions.= " secondary_positions.position_code in ($positionsCodes) ";
            }

            $order = 'players.name';           

            if($orderField != null){

                if( strtolower($orderField) == 'player_age'){
                    $order = 'player_age';
                }

                if( strtolower($orderField) == 'division'){
                    $order = 'division_class_id';
                }
            }
            
            $sense = (isset($orderSense) && $orderSense !='ASC')?'DESC':'ASC';

            $language_code = (isset($language_code) && $language_code != null)? $language_code: 'GB';

            $page = (isset($page) && $page != null)? $page : 1;
            $cant = (isset($limit)&& $limit != null)? $limit :100;            

            $offset = ($page - 1) * $cant;

            $filters = array(                 
                "whereNationality" =>$whereNationality,
                "whereSecondPositions" =>$whereSecondPositions,
                "joinSecondPositions" =>$joinSecondPositions, 
                "where" => $where,
                "order" => $order,
                "sense" => $sense,
                "offset" => $offset,
                "cant" => $cant
            );

            return $filters;

        }

        
        public function getAvailablePlayersWithFilters(
            $continent_code=null, 
            $country_code = null, 
            $category_id = null,
            $division_id = null,
            $club_id = null,
            $nationality_code = null, 
            $position_id = null,
            $second_positions_codes = null,
            $age_range = null,
            $height_range = null,
            $weight_range = null,
            $foot = null,
            $orderField = null,
            $orderSense = null,
            $page = null,
            $limit = null,
            $language_code = null,
            $user_id = null

        )
        {
            $db = parent::getDataBase();

            $filters = $this->getFiltersPlayersWithFilters(
                $continent_code, 
                $country_code, 
                $category_id,
                $division_id,
                $club_id,
                $nationality_code, 
                $position_id,
                $second_positions_codes,
                $age_range,
                $height_range,
                $weight_range,
                $foot,
                $orderField,
                $orderSense,
                $page,
                $limit,
                $language_code
            );

            $query = $this->getQueryPlayersWithFilters(
                $db,                 
                $filters["whereNationality"],
                $filters["whereSecondPositions"],
                $filters["joinSecondPositions"], 
                $filters["where"],
                $language_code,
                $user_id
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


        public function getAvailablePlayersWithFiltersTotalRows(
            $continent_code=null, 
            $country_code = null, 
            $category_id = null,
            $division_id = null,
            $club_id = null,
            $nationality_code = null, 
            $position_id = null,
            $second_positions_codes = null,
            $age_range = null,
            $height_range = null,
            $weight_range = null,
            $foot = null,
            $orderField = null,
            $orderSense = null,
            $page = null,
            $limit = null,
            $language_code = null

        )
        {
            $db = parent::getDataBase();

            $filters = $this->getFiltersPlayersWithFilters(
                $continent_code, 
                $country_code, 
                $category_id,
                $division_id,
                $club_id,
                $nationality_code, 
                $position_id,
                $second_positions_codes,
                $age_range,
                $height_range,
                $weight_range,
                $foot,
                $orderField,
                $orderSense,
                $page,
                $limit,
                $language_code
            );

            $mainQuery = $this->getQueryPlayersWithFilters(
                $db,                 
                $filters["whereNationality"],
                $filters["whereSecondPositions"],
                $filters["joinSecondPositions"], 
                $filters["where"],
                $language_code,
                null
            );          

            $query = "SELECT count(*) AS totalrows FROM (
                $mainQuery
                ) AS registros";           

            $datos = parent::obtenerDatos($query);                    
 
            return intval($datos[0]['totalrows']);
        }


        public function getPlayersRangesOfMeasures()
        {
            $db = parent::getDataBase();

            $query = "
            SELECT 
            max(height) AS max_height,
            min(height) AS min_height,
            max(weight) AS max_weight,
            min(weight) AS min_weight,
            max(TIMESTAMPDIFF(YEAR,players.birthdate,CURDATE())) AS max_age,
            min(TIMESTAMPDIFF(YEAR,players.birthdate,CURDATE())) AS min_age
            FROM $db.players";        

            $datos = parent::obtenerDatos($query);           
 
            return $datos;
        }
       


    }