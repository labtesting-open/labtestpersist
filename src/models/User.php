<?php

    namespace Elitelib;  


    class User extends Connect{

        public function listUsers($pagina = 1){

            $db = parent::getDataBase();

            $inicio = 0;
            $cantidad = 100;

            if($pagina > 1){                
                $inicio = ($cantidad * ($pagina - 1)) + 1 ;
                $cantidad = $cantidad * $pagina;
            }
            
            $query = "
            SELECT
            u.id,
            u.user_id,
            u.plan_id,
            p.name,
            u.estado,
            u.country_code,
            p.service_stream,
            p.service_admin_own_club,
            p.service_info_others_clubs,
            p.service_info_others_players
            FROM $db.users u 
            LEFT OUTER JOIN $db.plans p ON u.plan_id = p.id 
            limit $inicio,$cantidad";


            $datos = parent::obtenerDatos($query);           

            return $datos;

        }


        public function getUserInfo($id){

            $db = parent::getDataBase();
            
            $imgFileUsers = $this->getImgFolderUsers();
            
            $query = "
            SELECT            
            u.user_id,
            u.name,
            u.surname,
            u.plan_id,
            u.country_code,
            p.name as plan_name,           
            IF( ISNULL(u.img_perfil_url), null,
				CONCAT('$imgFileUsers',u.img_perfil_url)
            ) AS img_perfil_url,           
            IF(u.active=1,'true','false') as active          
            FROM $db.users u 
            LEFT OUTER JOIN $db.plans p ON u.plan_id = p.id 
            WHERE u.id=$id";

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }


       


        public function getPlanServices($plan_id){
            
            $db = parent::getDataBase(); 

            $query = "
            SELECT
            s.service_id as id,
            v.name,
            IF(s.active=1,'true','false') as active,
            v.url
            FROM $db.plan_service s
            LEFT OUTER JOIN $db.services v ON s.service_id = v.id 
            WHERE s.plan_id=$plan_id";

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }



    }
