<?php

    namespace Elitelib;     


    class SavedSearch extends Connect
    {

        public function add(           
            $user_id, 
            $target, 
            $paramsJSON,
            $searchResult
        )
        {
            $db = parent::getDataBase();

            $query="INSERT INTO $db.saved_searchs(user_id, target, params, result)
            VALUES($user_id, '$target','$paramsJSON',$searchResult)";
            
            $verifica = parent::nonQuery($query);

            return ($verifica)? 1 : 0;

        }

        public function get(           
            $user_id = null,             
            $target = null,
            $saved_search_id = null
        )
        {
            $db = parent::getDataBase();

            $where="";
           
            if($user_id != null)
            {
             $where.=(empty($where))?' WHERE ':' and ';
             $where.= " user_id=$user_id ";
            }
 
            if($target != null)
            {
             $where.=(empty($where))?' WHERE ':' and ';
             $where.= " target='$target' ";
            }

            if($saved_search_id != null)
            {
             $where.=(empty($where))?' WHERE ':' and ';
             $where.= " id=$saved_search_id ";
            }

            $query="SELECT
            id AS saved_search_id
            ,params AS params_json
            ,date_update 
            FROM  $db.saved_searchs
            $where";
            
            $datos = parent::obtenerDatos($query);           

            return $datos;

        }




    }