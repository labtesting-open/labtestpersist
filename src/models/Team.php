<?php   

    namespace Elitelib;

    class Team extends Connect{
       
        private $folder_team ="imgs/teams_profile/";      


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



    }