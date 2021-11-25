<?php

    namespace Elitelib;  

    class HostConnection
    {

        public $development = array("connection:"=>array(
            "server" => "localhost",
            "user" => "root",
            "password"   => "",
            "database" => "elites17_wizard",
            "port"  =>"3306",
            "img_folder_team" => "labtest/wizard_images/teams/"
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
    
