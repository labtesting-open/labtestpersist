<?php    

    namespace Elitelib; 

    class Season extends Connect{         


        public function getSeasonsByClubTeam($club_id, $team_id){
            
            $db = parent::getDataBase(); 

            $query = "
            SELECT
            seasons.id,
            CONCAT(seasons.begin, '/',seasons.end) as season
            FROM $db.seasons seasons
            INNER JOIN(
            SELECT season_id, 
            COUNT(DISTINCT id) AS matches_played
            FROM $db.matches 
            WHERE (club_id_home = $club_id and team_id_home = $team_id) OR
            (club_id_visitor = $club_id and team_id_visitor = $team_id)
            GROUP BY season_id
            ) matches_counted On matches_counted.season_id = seasons.id
            ORDER by seasons.begin desc" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }      



    }