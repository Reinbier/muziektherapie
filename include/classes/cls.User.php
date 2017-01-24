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

    public function insertUser($aParams, $role)
    {
        // already add date_added
        $fields = "Date_added";
        $values = ":date_added";
        $aQueryParams = array(":date_added" => array(date("Y-m-d H:i:s"), PDO::PARAM_STR));

        // prepare query vars
        foreach ($aParams as $columnName => $value)
        {
            $fields .= ", " . $columnName;
            $values .= ", " . $this->getEncryptValueString(":" . $columnName);
            $aQueryParams[":" . $columnName] = array($value, PDO::PARAM_STR);
        }

        // insert user
        $sql = "INSERT INTO USER (" . $fields . ")
                VALUES (" . $values . ")";
        $insertedID = $this->query($sql, $aQueryParams);

        // add role to the user
        return $this->insertUserRole($insertedID, $role);
    }

    public function insertUserRole($userID, $roleName)
    {
        $cRole = new Role();

        $sql = "INSERT INTO USER_ROLE (RoleID, UserID)
                VALUES (:roleid, :userid)";
        return $this->query($sql, array(
                    ":roleid" => array($cRole->getRoleIDByName($roleName), PDO::PARAM_INT),
                    ":userid" => array($userID, PDO::PARAM_INT)
        ));
    }

    public function getAllUsers($roles = null)
    {
        $fields = $this->getDecryptedTableFields("USER");

        if (is_null($roles))
        {
            $sql = "SELECT " . $fields . "
                    FROM USER";
        }
        else
        {
            $roles = convertRolesToStringForQuery($roles);
            
            $sql = "SELECT " . $fields . "
                FROM USER
                WHERE UserID IN (SELECT a.UserID
                                FROM USER_ROLE a, ROLE b
                                WHERE b.Role_name IN (" . $roles . ")
                                AND a.RoleID = b.RoleID)
                ORDER BY Name";
        
        }
        $result = $this->query($sql);

        return $result;
    }

    public function getAllClients()
    {
        $cRole = new Role();

        $fields = $this->getDecryptedTableFields("USER");
        $userRoleID = $cRole->getRoleIDByName("Client");

        $sql = "SELECT " . $fields . "
                FROM USER
                WHERE UserID IN (SELECT UserID
                                FROM USER_ROLE
                                WHERE RoleID = :roleid)
                ORDER BY Name";
        $result = $this->query($sql, array(
            ":roleid" => array($userRoleID, PDO::PARAM_INT)
        ));

        return $result;
    }

    /**
     * Will get all subjects of a therapist where they have common treatments
     * 
     * @param int $therapistID
     * @return object
     */
    public function getAllSubjects($therapistID)
    {
        $sql = "SELECT *
                FROM TREATMENT_USER
                WHERE TreatmentID IN (SELECT TreatmentID
                                      FROM TREATMENT_USER
                                      WHERE UserID = :therapistid)";
        $result = $this->query($sql, array(
                    ":therapistid" => array($therapistID, PDO::PARAM_INT)
        ));
        return $result;
    }

    public function getAllTherapists()
    {
        $cRole = new Role();

        $fields = $this->getDecryptedTableFields("USER");
        $userRoleID = $cRole->getRoleIDByName("Therapeut");

        $sql = "SELECT " . $fields . "
                FROM USER
                WHERE UserID IN (SELECT UserID
                                FROM USER_ROLE
                                WHERE RoleID = :roleid)
                ORDER BY Name";
        $result = $this->query($sql, array(
            ":roleid" => array($userRoleID, PDO::PARAM_INT)
        ));

        return $result;
    }

    public function getUsersByTreatmentID($treatmentID)
    {
        $fields = $this->getDecryptedTableFields("USER");
        $query = "SELECT " . $fields . "
                  FROM USER
                  WHERE UserID IN (SELECT UserID 
                                   FROM TREATMENT_USER
                                   WHERE TreatmentID = :treatmentID)
                  ORDER BY Name";
        $result = $this->query($query, array(
            ":treatmentID" => array(
                $treatmentID,
                PDO::PARAM_INT,
            ),
        ));
        return $result;
    }

}
