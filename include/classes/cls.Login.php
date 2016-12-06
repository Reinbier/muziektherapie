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
    
    /**
     * Checks if the user can be logged in with the email- and password-combination, given in the constructor.
     * This method also creates a login-session.
     * 
     * @return boolean  whether the user has been successfully logged in or not
     */
    public function loginUser()
    {
        $cUser = new User();
        $userData = $cUser->getUserByEmail($this->email);
        
        // does a user exist with this email?
        if(!is_null($userData))
        {
            // do the passwords match?
            if($this->checkPassword($this->password, $userData->Password))
            {
                $_SESSION["userID"] = $userData->UserID;
                return true;
            }
        }
        return false;
    }
    
    public function checkPassword($password, $dbPassword)
    {
        return $password == $dbPassword;
    }
}