<?php

    namespace Elitelib;  


    class FavouriteAction extends Connect{        


        public function getActionsByUser($user_id, $match_action_id = null){

            $db = parent::getDataBase();

            $where = '';
            
            if($user_id != null){
                $where.=' WHERE ';                
                $where.= " user_id = '$user_id'";
            }

            if($match_action_id != null){
                $where.=(empty($where))?' WHERE ':' and ';                
                $where.= " match_action_id = '$match_action_id'";
            }
            
            $query = "
            SELECT
            match_action_id 
            ,user_id
            ,date_added 
            FROM $db.users_favorites_actions
            $where
            ORDER BY date_added DESC";

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }        
        

        public function add($user_id, $match_action_id)
        {           

            $db = parent::getDataBase();            

            $query="INSERT INTO $db.users_favorites_actions(user_id, match_action_id, date_added)
            VALUES ($user_id, $match_action_id, date(now()))";

            $verifica = parent::nonQuery($query);
 
            return ($verifica)? 1 : 0;           

        }


        public function delete($user_id, $match_action_id)
        {
            $db = parent::getDataBase();            

            $query="DELETE FROM $db.users_favorites_actions 
            WHERE user_id=$user_id and match_action_id=$match_action_id";                   

            $affected = parent::nonQuery($query);
            
            return $affected;          

       }
       



    }
