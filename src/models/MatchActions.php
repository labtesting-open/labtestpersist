<?php

    namespace Elitelib;  


    class MatchActions extends Connect{      


        public function getSeasons(
            $player_id,
            $season_id = null,
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

            $sense = ( $order_sense != null && $order_sense =='DESC')?" DESC ":" ASC ";

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
            matches.season_id, 
            seasons.begin, 
            seasons.end, 
            COALESCE(matchesSeasons.matches_count, 0) AS matches
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

            $sense = ( $order_sense != null && $order_sense =='DESC')?" DESC ":" ASC ";

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
            matches.season_id,
            matches.id AS match_id,
            matches.match_date,
            clubsHome.name AS club_home_name,            
            clubsVisitor.name AS club_visitor_name,
            matches.goals_home_team,
            matches.goals_visitor_team,
            COUNT(match_actions.action_id) AS actions_in_match
            FROM $db.match_actions match_actions
            INNER JOIN $db.matches matches
                ON matches.id = match_actions.match_id
            LEFT JOIN $db.clubs clubsHome
                ON clubsHome.id = matches.club_id_home
            LEFT JOIN $db.clubs clubsVisitor 
                ON clubsVisitor.id = matches.club_id_visitor 
            $where
            GROUP BY matches.id
            ORDER BY $orderfields $sense";

            $rows = parent::obtenerDatos($query);    
            
            return $rows;

        }


       
        public function getActions(
            $player_id,
            $season_id = null,
            $match_id = null, 
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

            $sense = ( $order_sense != null && $order_sense =='DESC')?" DESC ":" ASC ";

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
            match_actions.action_id,
            actions.name AS action_name,
            COUNT(match_actions.action_id) AS action_count
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
       
        public function getFunctionExample()
        {
            return null;
        }


        



    }
