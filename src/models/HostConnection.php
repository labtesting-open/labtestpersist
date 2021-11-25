<?php

    namespace Elitelib;  

    class HostConnection
    {

        public $development = array("connection:"=>array(
            'server' => 'localhost',
            'user' => 'root',
            'password'   => '',
            'database' => 'elites17_wizard',
            'port'  =>'3306',
            'img_folder_team' => 'labtest/wizard_images/teams/',
            'img_folder_clubs' => 'labtest/wizard_images/clubs/',
            'img_folder_flags' => 'labtest/wizard_images/flags/',
            'img_folder_player_header' => 'labtest/wizard_images/player_header/',
            'img_folder_player_profile' => 'labtest/wizard_images/player_profile/',
            'img_folder_user' => 'labtest/wizard_images/user/',
        ));

        public $infinity = array("connection:"=>array(
            "server" => "localhost",
            "user" => "root",
            "password"   => "",
            "database" => "elites17_wizard",
            "port"  =>"3306"
        ));


        public function getParams()
        {   
            return $this->development;
        }
        
    }   
    
