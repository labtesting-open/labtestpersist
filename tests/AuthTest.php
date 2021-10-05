<?php

declare(strict_types=1);

use PHPUnit\Framework\TestCase;

use function PHPUnit\Framework\isNull;

class AuthTest extends TestCase{

    public $user = null;    
    public $auth_model;   

    protected function setUp(): void
    {
        $this->auth_model = new Elitelib\Auth();
        $this->user = 'admin@wizard.com';        
    }

    public function test_getUserAndGenerateNewToken()
    {   

        $data_user = $this->auth_model->getUserDataByUserName($this->user);
        $user_id = $data_user[0]['id'];
        var_dump($data_user);

        $token = $this->auth_model->insertarToken($user_id);        
        var_dump($token);

        $this->assertFalse(empty($token));      

    }

    public function test_getUserByToken(){

        $data_user = $this->auth_model->getUserDataByUserName($this->user);
        $user_id = $data_user[0]['id'];

        $token = $this->auth_model->insertarToken($user_id); 

        $data_user_token = $this->auth_model->getUserToken($token); 
        var_dump($data_user_token);

        $this->assertFalse(empty($data_user_token)); 
      
    }


    public function test_disableToken(){

        $data_user = $this->auth_model->getUserDataByUserName($this->user);
        $user_id = $data_user[0]['id'];

        $token = $this->auth_model->insertarToken($user_id); 
        
        $data_user_token = $this->auth_model->getUserToken($token); 
        
        $val = $this->auth_model->disableToken($data_user_token[0]['id']);
        $this->assertEquals(1, $val);
    }
    

  



    




}
