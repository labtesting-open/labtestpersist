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

        public function getFavouritePlayersTotalRows(
            $user_id = null 
            ,$player_id = null
            ,$orderField = null
            ,$orderSense = null
            ,$page = null
            ,$limit = null
            ,$language_code = null
        )
        {
            $db = parent::getDataBase();

            $filters = $this->getFiltersFavouritePlayers(
                $user_id
                ,$player_id
                ,$orderField
                ,$orderSense
                ,$page
                ,$limit
                ,$language_code
            );

            $mainQuery = $this->getQueryFavouritePlayers(
                $db              
                ,$filters["where"]
                ,$language_code
                ,$user_id                
            );    

            $query = "SELECT count(*) AS totalrows FROM (
                $mainQuery
                ) AS registros";           

            $datos = parent::obtenerDatos($query);                    
 
            return intval($datos[0]['totalrows']);

        }
        

        public function getFavouritePlayers(
            $user_id = null 
            ,$player_id = null
            ,$orderField = null
            ,$orderSense = null
            ,$page = null
            ,$limit = null
            ,$language_code = null
        )
        {
            $db = parent::getDataBase();

            $filters = $this->getFiltersFavouritePlayers(
                $user_id
                ,$player_id
                ,$orderField
                ,$orderSense
                ,$page
                ,$limit
                ,$language_code
            );

            $query = $this->getQueryFavouritePlayers(
                $db              
                ,$filters["where"]
                ,$language_code
                ,$user_id                
            );

            $order = $filters["order"];
            $sense = $filters["sense"];
            $offset = $filters["offset"];
            $cant = $filters["cant"];

            $query.= " 
            ORDER BY $order $sense 
            LIMIT $offset,$cant";           

            $datos = parent::obtenerDatos($query);           
 
            return $datos;

        }

        private function getFiltersFavouritePlayers(
            $user_id = null 
            ,$player_id = null
            ,$orderField = null
            ,$orderSense = null
            ,$page = null
            ,$limit = null
            ,$language_code = null
        )
        {
            $where = '';
            
            if($user_id != null){
                $where.=' WHERE ';                
                $where.= " favourites.user_id = $user_id";
            }

            if($player_id != null){
                $where.=(empty($where))?' WHERE ':' and ';                
                $where.= " favourites.player_id = $player_id";
            }

            $order = 'favourites.date_added';           

            if($orderField != null){
                if( strtolower($orderField) == 'player_name'){
                    $order = 'player_fullname';
                }
                if( strtolower($orderField) == 'date_added'){
                    $order = 'favourites.date_added';
                }
            }
            
            $sense = (isset($orderSense) && $orderSense !='ASC')?'DESC':'ASC';

            $language_code = (isset($language_code) && $language_code != null)? $language_code: 'GB';

            $page = (isset($page) && $page != null)? $page : 1;
            $cant = (isset($limit)&& $limit != null)? $limit :100;            

            $offset = ($page - 1) * $cant;

            $filters = array( 
                "where" => $where,
                "order" => $order,
                "sense" => $sense,
                "offset" => $offset,
                "cant" => $cant
            );

            return $filters;

        }

        
        public function getQueryFavouritePlayers(
            $db
            ,$where
            ,$language_code
            ,$user_id = null            
        )
        {
         
            $language_code = (isset($language_code))? $language_code: 'GB';

            $imgFolderClub = $this->getImgFolderClubs();
            $imgFolderFlags = $this->getImgFolderFlags();
            $imgFolderPlayersProfile = $this->getImgFolderPlayerProfiles();

            $own_favourite_field ='';
            $own_favourite_join ='';

            if($user_id != null && is_numeric($user_id)){

                $own_favourite_field = ",IF( ISNULL(user_id_mark), 'false', 'true') AS own_favourite ";
                $own_favourite_join = "
                LEFT JOIN (
                    SELECT
                    player_id
                    ,date_added
                    ,date_news_checked 
                    ,user_id AS user_id_mark 
                    FROM $db.users_favorites_players
                    where user_id = $user_id
                ) AS favorites ON favorites.player_id = players.id";

            }

            $query="
            SELECT
            favourites.player_id 
            ,CONCAT(players.name, ' ', players.surname) AS player_fullname
            ,TIMESTAMPDIFF(YEAR,players.birthdate,CURDATE()) AS player_age
            ,IF( ISNULL(players.img_profile), null,CONCAT('$imgFolderPlayersProfile', players.img_profile)) AS img_profile_url
            ,clubs.name AS club_name
            ,IF( ISNULL(clubs.logo), null,CONCAT('$imgFolderClub', clubs.logo)) AS club_logo
            ,colorposition.color_hexa
            ,positions.name as map_position_name
            ,nacionalities.nacionalities_names
            ,nacionalities.nacionalities_flags
            $own_favourite_field

            FROM $db.users_favorites_players favourites

            LEFT OUTER JOIN $db.players players 
            ON players.id = favourites.player_id

            LEFT OUTER JOIN $db.map_position_translate positions 
            ON positions.code = players.map_position and positions.translate_code='$language_code'

            LEFT JOIN $db.clubs clubs 
            ON clubs.id = players.club_id

            LEFT OUTER JOIN $db.positions colorposition
            ON colorposition.id = players.position_id

            $own_favourite_join

            LEFT JOIN (
            SELECT 
            player_nacionality.player_id AS player_id, 
            GROUP_CONCAT(countries.name) AS nacionalities_names,
            GROUP_CONCAT('$imgFolderFlags',countries.country_code,'.svg') AS nacionalities_flags
            FROM $db.players_nacionalities player_nacionality
            LEFT JOIN $db.country_codes countries
            ON countries.country_code = player_nacionality.country_code
            GROUP BY player_nacionality.player_id
            ) nacionalities ON nacionalities.player_id = players.id
            $where";

            return $query;

        }
        

        public function add($user_id, $player_id)
        {           

            $db = parent::getDataBase();            

            $query="INSERT INTO $db.users_favorites_players(user_id, player_id, date_added, date_news_checked)
            VALUES ($user_id, $player_id, date(now()), date(now()))";

            $verifica = parent::nonQuery($query);
 
            return ($verifica)? 1 : 0;           

        }

        public function addList($user_id, $arrayList)
        {           

            $db = parent::getDataBase();            

            $query="INSERT INTO $db.users_favorites_players(
                user_id, 
                player_id, 
                date_added, 
                date_news_checked)VALUES";
            
            foreach($arrayList AS $key => $value)
            {
                $query.=($key > 0)?',':'';
                $query.="($user_id, $value, date(now()), date(now()) )";
            }                

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

       public function deleteList($user_id, $arrayList)
        {
            $stringlist = implode(',', $arrayList);

            $db = parent::getDataBase();            

            $query="DELETE FROM $db.users_favorites_players 
            WHERE user_id=$user_id and player_id in ($stringlist)";                   

            $affected = parent::nonQuery($query);
            
            return $affected;          

       }

       function validateDate($string)
       {  
           $valid = false;

           // If the string can be converted to UNIX timestamp
           if (strtotime($string)) $valid = true;           
           
           return $valid;
        
       }      


       public function updateDateChecked(
           $user_id = null, 
           $player_id = null,
           $dateNewsChecked = null
        )
        {
            $db = parent::getDataBase();

            $where="";
           
            if($user_id != null)
            {
             $where.=(empty($where))?' WHERE ':' and ';
             $where.= " user_id=$user_id ";
            }
 
            if($player_id != null)
            {
             $where.=(empty($where))?' WHERE ':' and ';
             $where.= " player_id=$player_id ";
            }
            
            $dateSet = " date(now()) ";

            if($dateNewsChecked != null && $this->validateDate($dateNewsChecked))
            {            
                $dateSet = "'$dateNewsChecked' ";
            }

            $query="UPDATE $db.users_favorites_players
            set date_news_checked = $dateSet
            $where";                   

            $affected = parent::nonQuery($query);
            
            return $affected;          

       }

       public function getTotalNewActions($user_id)
       {

        $db = parent::getDataBase();

        $query = "
        SELECT
        count(*) AS total_actions
        FROM $db.match_actions match_actions

        INNER JOIN(
        SELECT
        favorites_players.player_id
        ,favorites_players.date_added AS date_player_in_favourites
        ,favorites_players.date_news_checked AS date_news_ckecked
        FROM $db.users_favorites_players favorites_players
        WHERE favorites_players.user_id = $user_id
        ) favourites ON favourites.player_id = match_actions.player_id
        
        WHERE match_actions.date_added > date_news_ckecked
        AND match_actions.id NOT IN (
        SELECT
        match_action_id 
        FROM $db.users_match_actions_views 
        WHERE user_id = $user_id)";

        $datos = parent::obtenerDatos($query);           

        return (int)$datos[0]['total_actions'];

       } 


       
       public function getPlayerNews($user_id, $language_code = null)
       {

        $db = parent::getDataBase(); 
        
        $language_code = (isset($language_code))? $language_code: 'GB';

        $imgFolderClub = $this->getImgFolderClubs();
        $imgFolderFlags = $this->getImgFolderFlags();
        $imgFolderPlayersProfile = $this->getImgFolderPlayerProfiles();        

        $query = "
        SELECT
        match_actions.player_id
        ,COUNT(match_actions.id) AS total_match_actions       
        ,CONCAT(players.name, ' ', players.surname) AS player_fullname
        ,IF( ISNULL(players.img_profile), null,CONCAT('$imgFolderPlayersProfile', players.img_profile)) AS img_profile_url
        ,clubs.name AS club_name
        ,IF( ISNULL(clubs.logo), null,CONCAT('$imgFolderClub', clubs.logo)) AS club_logo
        ,positions.name as map_position_name
        ,colorposition.color_hexa
        ,nacionalities.nacionalities_names
        ,nacionalities.nacionalities_flags
        ,match_actions.date_added AS date_match_action_added
        ,date_news_ckecked        

        FROM $db.match_actions match_actions        
            
        LEFT JOIN $db.players players
            ON players.id = match_actions.player_id    

        LEFT JOIN $db.clubs clubs 
            ON clubs.id = players.club_id
            
        LEFT OUTER JOIN $db.map_position_translate positions 
            ON positions.code = players.map_position and positions.translate_code='$language_code'
            
        LEFT OUTER JOIN $db.positions colorposition
            ON colorposition.id = players.position_id

        LEFT JOIN (
            SELECT 
            player_nacionality.player_id AS player_id, 
            GROUP_CONCAT(countries.name) AS nacionalities_names,
            GROUP_CONCAT('$imgFolderFlags',countries.country_code,'.svg') AS nacionalities_flags
            FROM $db.players_nacionalities player_nacionality
            LEFT JOIN $db.country_codes countries
            ON countries.country_code = player_nacionality.country_code
            GROUP BY player_nacionality.player_id
        ) nacionalities ON nacionalities.player_id = players.id                    
        
        INNER JOIN(
        SELECT
        favorites_players.player_id
        ,favorites_players.date_added AS date_player_in_favourites
        ,favorites_players.date_news_checked AS date_news_ckecked
        FROM $db.users_favorites_players favorites_players
        WHERE favorites_players.user_id = $user_id
        ) favourites ON favourites.player_id = match_actions.player_id

        WHERE match_actions.date_added > date_news_ckecked        
        GROUP BY match_actions.player_id";                   

        $datos = parent::obtenerDatos($query);           

        return $datos;
       }


       public function setActionListAsViewed($user_id, $arrayList)
       {           

           $db = parent::getDataBase();            

           $query="INSERT INTO $db.users_match_actions_views(
               user_id, 
               match_action_id, date_viewed)VALUES";
           
           foreach($arrayList AS $key => $value)
           {
               $query.=($key > 0)?',':'';
               $query.="($user_id, $value, date(now()))";
           }                

           $verifica = parent::nonQuery($query);

           return ($verifica)? 1 : 0;           

       }

       public function setPlayerListAsViewed(
           $user_id = null, 
           $arrayList = null,
           $dateNewsChecked = null
        )
        {           

            $db = parent::getDataBase();

            $dateSet = " date(now()) ";
            
            if($dateNewsChecked != null && $this->validateDate($dateNewsChecked))
            {            
                $dateSet = "'$dateNewsChecked' ";
            }

            $where="";
           
            if($user_id != null)
            {
                $where.=(empty($where))?' WHERE ':' and ';
                $where.= " user_id=$user_id ";
            }

            if(!empty($arrayList)){

                $arrToString = implode(',',$arrayList );

                $where .= " and player_id in ($arrToString) ";
            }                        

            $query="UPDATE $db.users_favorites_players
            set date_news_checked = $dateSet
            $where";                            

            $verifica = parent::nonQuery($query);

            return ($verifica)? 1 : 0;           

        }




       public function setActionAsViewed($user_id, $match_action_id)
       {           

           $db = parent::getDataBase();            

           $query="INSERT INTO $db.users_match_actions_views(
            user_id, 
            match_action_id, 
            date_viewed)VALUES(
            $user_id, 
            $match_action_id, 
            date(now()))";

           $verifica = parent::nonQuery($query);

           return ($verifica)? 1 : 0;          

       }

       public function deleteActionsViewed($user_id = null, $dateFrom = null)
       {          

           $db = parent::getDataBase();
           
           $where="";
           
           if($user_id != null)
           {
            $where.=(empty($where))?' WHERE ':' and ';
            $where.= " user_id=$user_id ";
           }

           if($dateFrom != null)
           {
            $where.=(empty($where))?' WHERE ':' and ';
            $where.= " date_viewed > '$dateFrom'";
           }

           $query="DELETE FROM $db.users_match_actions_views $where";                   

           $affected = parent::nonQuery($query);
           
           return $affected;          

      }

       



    }
