<?php

/**
 * @author: Reinier Gombert
 * @date: 22-nov-2016
 */
class User extends DAL
{

    private $userID;

    public function __construct($userID = null)
    {
        parent::__construct();

        $this->userID = $userID;
    }

    public function isTherapist()
    {
        $sql = "SELECT *
                FROM USER_ROLE a, ROLE b
                WHERE a.RoleID = b.RoleID
                AND a.UserID = :userid
                AND (b.Role_name = 'Therapeut' OR b.Role_name = 'Stagiair')";
        $result = $this->query($sql, array(
            ":userid" => array($this->userID, PDO::PARAM_INT)
        ));

        if (!is_null($result))
        {
            return true;
        }
        return false;
    }

    public function isClient()
    {
        $sql = "SELECT *
                FROM USER_ROLE a, ROLE b
                WHERE a.RoleID = b.RoleID
                AND a.UserID = :userid
                AND b.Role_name = 'Client'";
        $result = $this->query($sql, array(
            ":userid" => array($this->userID, PDO::PARAM_INT)
        ));

        if (!is_null($result))
        {
            return true;
        }
        return false;
    }

    /**
     * Method for retrieving user data
     * 
     * @param int $userID
     * @return object
     */
    public function getUserById($userID)
    {
        $fields = $this->getDecryptedTableFields("USER");
        
        $sql = "SELECT " . $fields . "
                FROM USER
                WHERE UserID = :userid";
        $result = $this->query($sql, array(
            ":userid" => array($userID, PDO::PARAM_INT)
        ), "one");

        return $result;
    }

    /**
     * Method for retrieving user data using his email
     * 
     * @param string $email
     * @return object
     */
    public function getUserByEmail($email)
    {
        $fields = $this->getDecryptedTableFields("USER");
        
        $sql = "SELECT " . $fields . "
                FROM USER
                WHERE Email = " . $this->getEncryptValueString(":email");
        $result = $this->query($sql, array(
            ":email" => array($email, PDO::PARAM_STR)
        ), "one");

        return $result;
    }

    public function insertUser($aParams)
    {
        
    }

}
