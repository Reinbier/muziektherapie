<?php

/**
 * @author: Reinier Gombert
 * @date: 24-nov-2016
 * 
 * Logs in a user
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
    
    /**
     * Check if two passwords match
     * 
     * @param type $password
     * @param type $dbPassword
     * @return type
     */
    public function checkPassword($password, $dbPassword)
    {
        return $password == $dbPassword;
    }
    
    /**
     * Check if a given users emailadres exists
     * 
     * @return boolean
     */
    public function checkEmailExists()
    {
        $cUser = new User();
        $userData = $cUser->getUserByEmail($this->email);
        
        // does a user exist with this email?
        if(!is_null($userData))
        {
            return true;
        }
        return false;
    }
}