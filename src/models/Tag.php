<?php    

    namespace Elitelib; 

    class Tag extends Connect{       

        public function get( $country_code = null){
            
            $db = parent::getDataBase(); 

            $countryCode = (empty($country_code))? 'GB': $country_code;

            $query = "
            SELECT
            tag
            ,name 
            FROM $db.tag_translate
            where country_code='$countryCode'" ;        

            $datos = parent::obtenerDatos($query);           

            return $datos;

        }


        public function add($tag, $contry_code, $name)
        {           

            $db = parent::getDataBase();            

            $query="INSERT INTO $db.tag_translate(tag, country_code, name)
            VALUES ('$tag', '$contry_code','$name')";

            $verifica = parent::nonQuery($query);
 
            return ($verifica)? 1 : 0;           

        }


        public function delete($tag, $contry_code)
        {
            $db = parent::getDataBase();            

            $query="DELETE FROM $db.tag_translate 
            WHERE tag='$tag' and country_code='$contry_code'";                   

            $affected = parent::nonQuery($query);
            
            return $affected;          

       }


       public function update($tag, $contry_code, $name)
        {           
            if(empty($tag) || empty($contry_code) || empty($name)) return 0;

            $db = parent::getDataBase();            

            $query="UPDATE $db.tag_translate            
            SET name='$name'
            WHERE tag='$tag' and country_code='$contry_code'";
            
            $verifica = parent::nonQuery($query);

            return ($verifica)? 1 : 0;            

        }


        



    }