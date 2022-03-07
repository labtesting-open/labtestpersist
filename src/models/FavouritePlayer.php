<?php

    namespace Elitelib;  


    class FavouritePlayer extends Connect{        


        public function getFavouritePlayersByUser($user_id, $player_id = null){

            $db = parent::getDataBase();

            $where = '';
            
            if($user_id != null){
                $where.=' WHERE ';                
                $where.= " user_id = '$user_id'";
            }

            if($player_id != null){
                $where.=(empty($where))?' WHERE ':' and ';                
                $where.= " player_id = '$player_id'";
            }
            
            $query = "
            SELECT
            player_id 
            ,user_id
            ,date_added
            ,date_news_checked 
            FROM $db.users_favorites_players
            $where
            ORDER BY date_added DESC";

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }        
        

        public function add($user_id, $player_id)
        {           

            $db = parent::getDataBase();            

            $query="INSERT INTO $db.users_favorites_players(user_id, player_id, date_added, date_news_checked)
            VALUES ($user_id, $player_id, date(now()), date(now()))";

            $verifica = parent::nonQuery($query);
 
            return ($verifica)? 1 : 0;           

        }


        public function delete($user_id, $player_id)
        {
            $db = parent::getDataBase();            

            $query="DELETE FROM $db.users_favorites_players 
            WHERE user_id=$user_id and player_id=$player_id";                   

            $affected = parent::nonQuery($query);
            
            return $affected;          

       }
       



    }
