<?php

/**
 * @author: Reinier Gombert
 * @date: 24-nov-2016
 */

class Login extends DAL
{

    private $email;
    private $password;

    public function __construct($email, $password)
    {
        parent::__construct();
        $this->email = $email;
        $this->password = $password;
    }
    
    public function loginUser()
    {
        $cUser = new User();
        $userData = $cUser->getUserByEmail($this->email);
        
        
    }
    
    public function checkPassword($password, $hashValue)
    {
        
    }
}