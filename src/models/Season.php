<?php    

    namespace Elitelib; 

    class Season extends Connect{         


        public function getSeasonsWithMatchesByClubTeam(
            $club_id, $team_id, $onlyWithMatches = null
        ){
            
            $db = parent::getDataBase();

            $where = '';
            
            if( $onlyWithMatches != null && $onlyWithMatches){
                $where = ' WHERE matches_played > 0 ';
            }

            $query = "
            SELECT
            seasons.id,
            CONCAT(seasons.begin, '/',seasons.end) as season,
            COALESCE(matches.matches_played, 0) as matches_played
            FROM $db.seasons seasons
            LEFT JOIN 
            (
                SELECT season_id, 
                COUNT(DISTINCT id) AS matches_played
                FROM $db.matches 
                WHERE (club_id_home = $club_id and team_id_home = $team_id) OR
                (club_id_visitor = $club_id and team_id_visitor = $team_id)
            ) AS matches ON matches.season_id = seasons.id
            $where
            ORDER BY seasons.id DESC" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }
        
        public function getAllSeasons(){
            
            $db = parent::getDataBase(); 

            $query = "
            SELECT
            seasons.id,
            CONCAT(seasons.begin, '/',seasons.end) as season
            FROM $db.seasons seasons            
            ORDER by seasons.begin desc" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }


        



    }