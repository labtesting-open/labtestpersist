<?php
    namespace Elitelib;  

    class Auth extends Connect{

        
        public function obtenerDatosUsuario($correo){

            $db = parent::getDataBase();
    
            $query = "SELECT id, user_id, password, active FROM $db.users WHERE user_id='$correo'";
            $datos = parent::obtenerDatos($query);
    
            if(isset($datos[0]['id'])){
                return $datos;
            }else{
                return 0;
            }
        }
    
    
        public function insertarToken($userId){
    
            $db = parent::getDataBase();
            $val = true;
            $token = bin2hex(openssl_random_pseudo_bytes(16,$val));
            $date = date("Y-m-d H:i");
            $estado = 1;
            $query = "INSERT INTO $db.user_token(user_id, token, mode, date_time)values($userId,'$token', $estado, '$date')";       
    
            
            $verifica = parent::nonQuery($query);
    
            if($verifica){
                return $token;
            }else{
                return 0;
            }
    
        }
    
        public function getUserToken($token){
    
            $db = parent::getDataBase();
    
            $query = "SELECT id, mode FROM $db.user_token WHERE token='$token'";
            $datos = parent::obtenerDatos($query);
    
            if(isset($datos[0]['id'])){
                return $datos;
            }else{
                return 0;
            }
    
        }
    
        public function disableToken($id){
    
            $db = parent::getDataBase();
            $val = true;               
            $query = "UPDATE $db.user_token SET mode = 0 WHERE id=$id";       
            
            $update = parent::nonQuery($query);
    
            if($update){
                return 1;
            }else{
                return 0;
            }
    
        }
       


    }