<?php

    namespace Elitelib; 


    class UserClub extends Connect{       


        public function getUserClub($user_id){
            
            $db = parent::getDataBase(); 

            $query = "
            SELECT
            club_id
            FROM $db.users_clubs 	            
            WHERE user_id=$user_id" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }      



    }