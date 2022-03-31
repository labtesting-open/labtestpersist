<?php    

    namespace Elitelib; 

    class Nationality extends Connect{         


        
        
        public function get(){
            
            $db = parent::getDataBase();
            $imgFolderFlags = $this->getImgFolderFlags();

            $query = "
            SELECT 
            nacionalities.player_id,
            group_concat(nacionalities.country_code) AS nationalities_codes ,
            group_concat( CONCAT('$imgFolderFlags', nacionalities.country_code,'.svg')) AS nationalities_flags,
            group_concat( country_codes.name) AS nationalities_names
            FROM $db.players_nacionalities nacionalities 
            LEFT JOIN $db.country_codes country_codes
            ON country_codes.country_code = nacionalities.country_code	
            GROUP by nacionalities.player_id
            ORDER BY nacionalities.player_id" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }


        public function add($player_id, $nationalitiesPlayerList)
        {
            if(empty($player_id) || empty($nationalitiesPlayerList)) return -1;

            $db = parent::getDataBase();
            
            $listArr = explode(',', $nationalitiesPlayerList);

            $query="INSERT INTO $db.players_nacionalities(`player_id`, `country_code`, `order`)VALUES";

            $index = 1;
            foreach($listArr as $value)
            {                
                $query.=($index > 1)?',':'';                
                $query .= "($player_id, $value, $index)";
                $index++;
            }           

            $affected = parent::nonQuery($query);
 
            return $affected;

        }

        public function delete($player_id)
        {
            if(empty($player_id)) return -1;

            $db = parent::getDataBase();

            $query="DELETE FROM $db.players_nacionalities WHERE player_id=$player_id";                       

            $affected = parent::nonQuery($query);
 
            return $affected;

        }




        



    }