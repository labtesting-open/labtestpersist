<?php

namespace Elitelib;

use mysqli;

class Connect{

    private $server;
    private $user;
    private $password;
    private $database;
    private $port;
    private $imgFolderteams;
    private $imgFolderClubs;
    private $imgFolderFlags;
    private $imgFolderPlayerHeader;
    private $imgFolderPlayerProfiles;
    private $imgFolderUsers;




    function __construct( $dataConnection = null){

        if(!isset($dataConnection)){
            $dataConnection = $this->getDataConnectionFromFile();
        }
        
        foreach($dataConnection as $key=>$value){
            $this->server   = $value['server'];
            $this->user     = $value['user'];
            $this->password = $value['password'];
            $this->database = $value['database'];
            $this->port     = $value['port'];
            $this->imgFolderteams = $value['img_folder_teams'];
            $this->imgFolderClubs = $value['img_folder_clubs'];
            $this->imgFolderFlags = $value['img_folder_flags'];
            $this->imgFolderPlayerHeaders = $value['img_folder_player_headers'];
            $this->imgFolderPlayerProfiles = $value['img_folder_player_profiles'];
            $this->imgFolderUsers = $value['img_folder_users'];
        }

        $this->conexion = new mysqli(
            $this->server,
            $this->user,
            $this->password,
            $this->database,
            $this->port
        );

        if($this->conexion->connect_errno){
            echo "Error en conexion";
            die();
        }else{
            $this->conexion->query('set names utf8');                    
        }

    }

    public function scapeParameter($parameter){

        return mysqli_real_escape_string($this->conexion,$parameter);      

    }



    private function getDataConnectionFromFile(){
        $direccion = dirname(__FILE__);
        $jsonData = file_get_contents($direccion."/"."config");
        return json_decode($jsonData, true);
    }


    private function convertirUTF8($array){

        array_walk_recursive($array, function(&$item, $key){
            if(!mb_detect_encoding($item, 'utf-8', true)){
                $item = utf8_encode($item);
            }
        });

        return $array;

    }

    public function getDataBase(){
        return $this->database;
    }

    public function obtenerDatos($sqlStr){

        $results = $this->conexion->query($sqlStr);

        $resultArray = array();
       
        foreach($results as $key){
            $resultArray[] = $key;
        }             

        return $this->convertirUTF8($resultArray);
        

    }


    public function nonQuery($sqlStr){
        $results = $this->conexion->query($sqlStr);
        return $this->conexion->affected_rows;
    }

    //solo para inserts
    public function nonQueryId($sqlStr){
        $results = $this->conexion->query($sqlStr);
        $filas = $this->conexion->affected_rows;
        if($filas >= 1){
            return $this->conexion->insert_id;
        }else{
            return 0;
        }

    }


    //encriptar
    public function encriptar($string){
        return md5($string);
    }


    public function getImgFolderTeams(){
        return $this->imgFolderteams;
    }

    public function getImgFolderClubs(){
        return $this->imgFolderClubs;
    }

    public function getImgFolderFlags(){
        return $this->imgFolderFlags;
    }

    public function getImgFolderPlayerHeaders(){
        return $this->imgFolderPlayerHeader;
    }

    public function getImgFolderPlayerProfiles(){
        return $this->imgFolderPlayerProfiles;
    }

    public function getImgFolderUsers(){
        return $this->imgFolderUsers;
    }


}