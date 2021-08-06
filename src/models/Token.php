<?php
    namespace Elitelib;  

    class Token extends Connect{

        
        public function checkToken($token){

            $db = parent::getDataBase();

            $query = "SELECT user_id FROM $db.user_token WHERE mode =1 and token='$token'";            

            $resp = parent::obtenerDatos($query);           

            if($resp){
                return $resp;
            }else{
                return 0;    
            }

        }


        public function updateTokens($fecha){

            $db = parent::getDataBase();

            $query = "UPDATE $db.user_token SET mode = 0 WHERE date_time < '$fecha' and mode=1";

            $verifica = parent::nonQuery($query);

            if($verifica > 0){
                //$this->escribirEntrada($verifica);
                return $verifica;
            }else{
                return 0;
            }

        }

        public function deleteOldTokens($fecha){

            $db = parent::getDataBase();

            $query = "DELETE FROM $db.user_token WHERE date_time < '$fecha' ";            

            $verifica = parent::nonQuery($query);

            if($verifica > 0){
               // $this->escribirEntrada($verifica);
                return $verifica;
            }else{
                return 0;
            }

        }

        private function crearTxt($direccion){
            $archivo = fopen($direccion, 'w') or die ("error al crear el archivo de registros");
            $texto = "------------------------------------ Registros del CRON JOB ------------------------------------ \n";
            fwrite($archivo,$texto) or die ("no pudimos escribir el registro");
            fclose($archivo);
        }


        private function escribirEntrada($registros){
            $direccion = "../cron/registros/registros.txt";
            if(!file_exists($direccion)){
                $this->crearTxt($direccion);
            }
            /* crear una entrada nueva */
            $this->escribirTxt($direccion, $registros);
        }
    
        private function escribirTxt($direccion, $registros){
            $date = date("Y-m-d H:i");
            $archivo = fopen($direccion, 'a') or die ("error al abrir el archivo de registros");
               $texto = "Se modificaron $registros registro(s) el dia [$date] \n";
               fwrite($archivo,$texto) or die ("no pudimos escribir el registro");
               fclose($archivo);
        }



    }