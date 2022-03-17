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

            $query="INSERT INTO $db.saved_searchs(user_id, target, params, result, date_update)
            VALUES($user_id, '$target','$paramsJSON',$searchResult, DATE(NOW()))";
            
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
            ,result
            ,date_update 
            FROM  $db.saved_searchs
            $where";
            
            $datos = parent::obtenerDatos($query);           

            return $datos;

        }

        public function delete(
            $saved_search_id = null, 
            $user_id = null, 
            $dateFrom = null
        )
        {          
 
            $db = parent::getDataBase();
            
            $where="";

            if($saved_search_id != null)
            {
             $where.=(empty($where))?' WHERE ':' and ';
             $where.= " id=$saved_search_id ";
            }
            
            if($user_id != null)
            {
             $where.=(empty($where))?' WHERE ':' and ';
             $where.= " user_id=$user_id ";
            }
 
            if($dateFrom != null)
            {
             $where.=(empty($where))?' WHERE ':' and ';
             $where.= " date_update >= '$dateFrom'";
            }
 
            $query="DELETE FROM $db.saved_searchs $where";                   
 
            $affected = parent::nonQuery($query);
            
            return $affected;          
 
       }


       public function update(           
        $saved_search_id,
        $paramsJSON = null,
        $searchResult = null        
        )
        {
            if(empty($saved_search_id)) return 0;
            
            $db = parent::getDataBase();
            
            $where =" WHERE id=$saved_search_id ";

            $set=" date_update = DATE(NOW())";

            if(!is_null($paramsJSON))
            {             
                $set.= ", params='$paramsJSON' ";
            }

            if(!is_null($searchResult))
            {             
                $set.= ", result=$searchResult ";
            }            


            $query="UPDATE $db.saved_searchs            
            SET $set
            $where";
            
            $verifica = parent::nonQuery($query);

            return ($verifica)? 1 : 0;            

        }




    }