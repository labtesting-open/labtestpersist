<?php

    namespace Elitelib;  


    class MatchActions extends Connect{      


        public function getSeasons(
            $player_id,
            $season_id = null,
            $season_id_selected = null,
            $match_id = null,
            $order = null, 
            $order_sense = null
        )
        {

            $db = parent::getDataBase();
            
            switch ($order) {
                
                case 'season_id':
                    $orderfields ="matches.season_id";
                    break;               
                default:
                    $orderfields ="matches.season_id";
            }            

            $sense = ( $order_sense != null && $order_sense =='ASC')?" ASC ":" DESC ";

            $selected = '';

            if ($season_id_selected != null) {
                $selected =",IF(matches.season_id=$season_id_selected, 'true','false') AS selected ";
            }

            $where='';

            if($player_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= "match_actions.player_id = $player_id ";                    
            }
            
            if($season_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= "matches.season_id = $season_id ";                    
            }

            if($match_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= "matches.id = $match_id ";                    
            }
            

            $query = "
            SELECT 
            matches.season_id 
            ,seasons.begin 
            ,seasons.end 
            ,COALESCE(matchesSeasons.matches_count, 0) AS matches
            $selected
            FROM $db.match_actions match_actions
            INNER JOIN $db.matches matches
                ON matches.id = match_actions.match_id	
            LEFT JOIN $db.seasons seasons
                ON seasons.id = matches.season_id
            LEFT JOIN(
                SELECT   
                matches.season_id  
                ,COUNT(matches.id) AS matches_count	
                FROM  $db.matches   
                GROUP BY matches.season_id
            ) AS matchesSeasons ON matchesSeasons.season_id = matches.season_id            
            $where
            GROUP BY matches.season_id
            ORDER BY $orderfields $sense";

            $rows = parent::obtenerDatos($query);    
            
            return $rows;

        }


        public function getMatches(
            $player_id,
            $season_id = null,
            $match_id = null,
            $match_id_selected_list = null,
            $order = null, 
            $order_sense = null
        )
        {

            $db = parent::getDataBase();
            
            switch ($order) {
                
                case 'match_date':
                    $orderfields ="matches.match_date";
                    break;               
                default:
                    $orderfields ="matches.match_date";
            }            

            $sense = ( $order_sense != null && $order_sense =='ASC')?" ASC ":" DESC ";

            $selected = '';

            if ($match_id_selected_list != null) {
                $selected =",IF(matches.id IN ($match_id_selected_list), 'true','false') AS selected ";
            }

            $where='';

            if($player_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= "match_actions.player_id = $player_id ";                    
            }
            
            if($season_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= "matches.season_id = $season_id ";                    
            }

            if($match_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= "matches.id = $match_id ";                    
            }

            $query = "
            SELECT
            matches.season_id
            ,matches.id AS match_id
            ,matches.match_date
            ,clubsHome.name AS club_home_name
            ,clubsVisitor.name AS club_visitor_name
            ,matches.goals_home_team
            ,matches.goals_visitor_team
            ,COUNT(match_actions.action_id) AS actions_in_match
            ,countries.name AS country_name
            ,division.name AS division_name
            $selected
            FROM $db.match_actions match_actions
            INNER JOIN $db.matches matches
                ON matches.id = match_actions.match_id
            LEFT JOIN $db.clubs clubsHome
                ON clubsHome.id = matches.club_id_home
            LEFT JOIN $db.clubs clubsVisitor 
                ON clubsVisitor.id = matches.club_id_visitor
            LEFT JOIN $db.country_codes countries
				ON countries.country_code = matches.country_code
			LEFT JOIN $db.division division
				ON division.id = matches.division_id
            $where
            GROUP BY matches.id
            ORDER BY $orderfields $sense";

            $rows = parent::obtenerDatos($query);    
            
            return $rows;

        }


       
        public function getActions(
            $player_id,
            $season_id = null,
            $match_id_list = null, 
            $action_id_select_list = null,
            $order = null, 
            $order_sense = null           
        )
        {

            $db = parent::getDataBase();
            
            switch ($order) {
                
                case 'action_name':
                    $orderfields ="actions.name";
                    break;
                case 'action_count':
                    $orderfields ="action_count";
                    break;              
                default:
                    $orderfields ="action_count";
            }            

            $sense = ( $order_sense != null && $order_sense =='ASC')?" ASC ":" DESC ";

            $selected = '';

            if ($action_id_select_list != null) {
                $selected =",IF(match_actions.action_id IN ($action_id_select_list), 'true','false') AS selected ";
            }

            $where='';

            if($player_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= "match_actions.player_id = $player_id ";                    
            }
            
            if($season_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= "matches.season_id = $season_id ";                    
            }

            if($match_id_list != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= "matches.id in ($match_id_list) ";                    
            }

            $query = "
            SELECT 
            match_actions.action_id
            ,actions.name AS action_name
            ,COUNT(match_actions.action_id) AS action_count
            $selected
            FROM 
            $db.match_actions match_actions
            INNER JOIN $db.matches matches
                ON matches.id = match_actions.match_id
            LEFT JOIN $db.actions actions
                ON actions.id = match_actions.action_id
            $where
            GROUP BY match_actions.action_id
            ORDER BY $orderfields $sense";

            $rows = parent::obtenerDatos($query);    
            
            return $rows;

        }
        
        
        public function getPlayerActions(
            $player_id,
            $season_id = null,
            $match_id_list = null, 
            $action_id_list = null,
            $order = null, 
            $order_sense = null
        ){

            $db = parent::getDataBase();
            
            switch ($order) {
                
                case 'match_date':
                    $orderfields ="match_actions.match_date";
                    break;
                case 'action_name':
                    $orderfields ="action_name";
                    break;              
                default:
                    $orderfields ="match_actions.match_date";
            }            

            $sense = ( $order_sense != null && $order_sense =='ASC')?" ASC ":" DESC ";           

            $where='';

            if($player_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= "match_actions.player_id = $player_id ";                    
            }
            
            if($season_id != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= "matches.season_id = $season_id ";                    
            }

            if($match_id_list != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= "matches.id in ($match_id_list) ";                    
            }
            
            if($action_id_list != null){
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= "match_actions.action_id in ($action_id_list) ";                    
            }   
           

            $query = "
            SELECT 
            match_actions.match_id
            ,match_actions.match_date
            ,actions.name AS action_name            
            ,clubsHome.name AS club_home_name
            ,clubsVisitor.name AS club_visitor_name
            ,matches.goals_home_team
            ,matches.goals_visitor_team
            ,match_actions.minute
            ,match_actions.url_video
                        
            FROM $db.match_actions match_actions

            INNER JOIN $db.matches matches
                ON matches.id = match_actions.match_id
                
            LEFT JOIN $db.actions actions
                ON actions.id = match_actions.action_id

            LEFT JOIN $db.clubs clubsHome
            ON clubsHome.id = matches.club_id_home 

            LEFT JOIN $db.clubs clubsVisitor 
            ON clubsVisitor.id = matches.club_id_visitor
            $where
            ORDER BY $orderfields $sense";        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }



    }
