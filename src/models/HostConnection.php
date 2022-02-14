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
            'img_folder_teams' => 'labtest/wizard_images/teams/',
            'img_folder_clubs' => 'labtest/wizard_images/clubs/',
            'img_folder_flags' => 'labtest/wizard_images/flags/',
            'img_folder_player_headers' => 'labtest/wizard_images/players_header/',
            'img_folder_player_profiles' => 'labtest/wizard_images/players_profile/',
            'img_folder_users' => 'labtest/wizard_images/users/',
            'img_folder_division' => 'labtest/wizard_images/divisions/',
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
    
