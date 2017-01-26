<?php

/**
 * @author: Patrick Pieper
 * @date: 06-12-2016
 */
class Treatment extends DAL
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Creates a new treatment
     * @param  String $name name of the treatment
     * @return int        unique ID of the inserted treatment.
     */
    public function createTreatment($name)
    {
        $sql = "INSERT INTO TREATMENT (Name)
                VALUES (:name)";

        $result = $this->query($sql, array(
            ":name" => array($name, PDO::PARAM_STR)));

        return $result;
    }
    
    public function finishTreatment($treatmentID)
    {
        $sql = "UPDATE TREATMENT
                SET Active = 0, 
                    End = NOW()
                WHERE TreatmentID = :tmt_id";
        return $this->query($sql, array(
            ":tmt_id" => array($treatmentID, PDO::PARAM_INT)
        ));
    }
    
    public function deleteTreatment($treatmentID)
    {
        $sql = "DELETE FROM TREATMENT
                WHERE TreatmentID = :tmt_id";
        return $this->query($sql, array(
            ":tmt_id" => array($treatmentID, PDO::PARAM_INT)
        ));
    }

    /**
     * Get all treatments for a user.
     * 
     * @param  int $userID 	  The ID of the user which treatments we have to find.
     * @return [type]         returns the treatments.
     */
    public function getTreatmentsByUserID($userID)
    {
        $sql = "SELECT *
                FROM TREATMENT_USER
                WHERE UserID = :userID
                ORDER BY TreatmentID";

        $result = $this->query($sql, array(
            ":userID" => array($userID, PDO::PARAM_INT))
        );

        return $result;
    }

    /**
     * Get treatment
     * 
     * @param  int $treatmentID 	  The ID of the treatment
     * @return [object]         returns the treatment data.
     */
    public function getTreatmentByTreatmentID($treatmentID)
    {
        $sql = "SELECT *
                FROM TREATMENT
                WHERE TreatmentID = :treatmentid";

        $result = $this->query($sql, array(
            ":treatmentid" => array($treatmentID, PDO::PARAM_INT)), "one"
        );

        return $result;
    }

    /**
     * Gets active treatment where the measurement is linked to.
     * 
     * @param  int $userID 	  The ID of the user which treatment we have to find.
     * @return [type]         returns the treatment ID of the treatment where user with $userID is patient.
     */
    public function getActiveTreatmentByUserID($userID)
    {
        $sql = "SELECT *
                FROM TREATMENT_USER a, TREATMENT b
                WHERE a.UserID = :userID
                AND a.TreatmentID = b.TreatmentID
                AND b.Active = 1";

        $result = $this->query($sql, array(
            ":userID" => array($userID, PDO::PARAM_INT)), "one");

        return $result;
    }
    
    /**
     * Method to figure out whether this treatment is active or not
     * 
     * @param int $treatmentID
     * @return boolean  returns null upon no result set found.
     */
    public function isActive($treatmentID)
    {
        $sql = "SELECT *
                FROM TREATMENT
                WHERE TreatmentID = :tmt_id";

        $result = $this->query($sql, array(
            ":tmt_id" => array($treatmentID, PDO::PARAM_INT)), "one");

        if(is_null($result))
        {
            return null;
        }
        else if($result->Active == '0')
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Get all measurements that belong to this treatmentID
     * 
     * @param int $treatmentId
     * @return object
     */
    public function getMeasurementsbyTreatmentID($treatmentId)
    {
        $sql = "SELECT *
                FROM MEASUREMENT
                WHERE TreatmentID = :treatmentid";

        $result = $this->query($sql, array(
            ":treatmentid" => array(
                $treatmentId,
                PDO::PARAM_INT),
        ));

        return $result;
    }
    
    /**
     * Get all users that are linked to this treatment
     * 
     * @param int $treatmentID
     * @return object
     */
    public function getAllUsersInTreatment($treatmentID)
    {
        $sql = "SELECT UserID
                FROM TREATMENT_USER
                WHERE TreatmentID = :treatmentid";

        $result = $this->query($sql, array(
            ":treatmentid" => array(
                $treatmentID,
                PDO::PARAM_INT),
        ));

        return $result;
    }

    /**
     * Get the id of the client that is part of this treatment
     * 
     * @param int $treatmentID
     * @return type
     */
    public function getClientIDbyTreatmentID($treatmentID)
    {
        $sql = "SELECT b.UserID
                FROM TREATMENT_USER b, USER_ROLE c, ROLE d
                WHERE b.TreatmentID = :treatmentid
                AND b.UserID = c.UserID
                AND c.RoleID = d.RoleID
                AND d.Role_name = 'Client'";

        $result = $this->query($sql, array(
            ":treatmentid" => array(
                $treatmentID,
                PDO::PARAM_INT),
        ), "column");

        return $result;
    }

    /**
     * Get the id of the therapist that is part of this treatment
     * 
     * @param int $treatmentID
     * @return type
     */
    public function getTherapistIDbyTreatmentID($treatmentID)
    {
        $sql = "SELECT b.UserID
                FROM TREATMENT_USER b, USER_ROLE c, ROLE d
                WHERE b.TreatmentID = :treatmentid
                AND b.UserID = c.UserID
                AND c.RoleID = d.RoleID
                AND d.Role_name = 'Therapeut'";

        $result = $this->query($sql, array(
            ":treatmentid" => array(
                $treatmentID,
                PDO::PARAM_INT),
        ), "column");

        return $result;
    }

    /**
     * Get the id's of the kin that is/are part of this treatment
     * 
     * @param int $treatmentID
     * @return type
     */
    public function getKinbyTreatmentID($treatmentID)
    {
        $sql = "SELECT b.UserID, d.Role_name
                FROM MEASUREMENT a, TREATMENT_USER b, USER_ROLE c, ROLE d
                WHERE a.TreatmentID = :treatmentid
                AND a.TreatmentID = b.TreatmentID
                AND b.UserID = c.UserID
                AND c.RoleID = d.RoleID
                AND d.Role_name IN ('Naaste', 'Professional')
                GROUP BY b.UserID";

        $result = $this->query($sql, array(
            ":treatmentid" => array(
                $treatmentID,
                PDO::PARAM_INT),
        ));

        return $result;
    }
    
    /**
     * Link an existing user to a treatment
     * 
     * @param int $userID
     * @param int $treatmentID
     * @return type
     */
    public function addUserToTreatment($userID, $treatmentID)
    {
        $sql = "INSERT INTO TREATMENT_USER (TreatmentID, UserID)
                VALUES (:treatmentid, :userid)";
        return $this->query($sql, array(
            ":treatmentid" => array($treatmentID, PDO::PARAM_INT),
            ":userid" => array($userID, PDO::PARAM_INT)
        ));
    }
    
    /**
     * Check if the given user is part of this treatment
     * 
     * @param int $userID
     * @param int $treatmentID
     * @return boolean
     */
    public function userInTreatment($userID, $treatmentID)
    {
        $sql = "SELECT *
                FROM TREATMENT_USER
                WHERE UserID = :userID
                AND TreatmentID = :treatmentID";

        $result = $this->query($sql, array(
            ":userID" => array($userID, PDO::PARAM_INT),
            ":treatmentID" => array($treatmentID, PDO::PARAM_INT)
            ));

        if(is_null($result))
        {
            return false;
        }
        else
        {
            return true;
        }
    }

    /**
     * Get the points of the graph for the user(s) based on which user requested this method.
     * 
     * @param int $treatmentID  The treatment of which the graph is needed
     * @param int $roleName     A therapist will get all of the lines for all of the users within this treatment, a client will only get his own line
     * @return array
     */
    public function drawGraph($treatmentID, $roleName)
    {
        $cQuestionList = new QuestionList();
        $cUser = new User();
        $cTreatment = new Treatment();
        $cMeasurement = new Measurement();
        $aParams = array();

        if ($roleName === 'Therapeut')
        {
            $treatment = $cTreatment->getTreatmentByTreatmentID($treatmentID);

            if (!is_null($treatment))
            {
                $measurements = $cTreatment->getMeasurementsbyTreatmentID($treatment->TreatmentID);

                if (!is_null($measurements))
                {
                    $aParams = array();
                    foreach ($measurements as $measurement)
                    {
                        $users = $cUser->getUsersByTreatmentID($treatment->TreatmentID);
                        $measurementName = $measurement->Name;
                        $aUsers = array();
                        foreach ($users as $user)
                        {
                            if ($cQuestionList->isComplete($measurement->MeasurementID, $measurement->QuestionlistID, $user->UserID))
                            {
                                $points = $cMeasurement->getPointsByUserID($measurement->MeasurementID, $user->UserID);
                                if (is_null($points))
                                {
                                    $points = 0;
                                }
                                $aUsers[$user->Name] = (int) $points;
                            }
                        }
                        arsort($aUsers);
                        $aParams[] = array("measurement" => $measurementName) + $aUsers;
                    }
                }
            }
        }
        else
        {
            // get treatment
            $treatment = $cTreatment->getTreatmentByTreatmentID($treatmentID);
            
            if (!is_null($treatment))
            {
                $userID = $cTreatment->getClientIDbyTreatmentID($treatmentID);
                $measurements = $cTreatment->getMeasurementsbyTreatmentID($treatment->TreatmentID);

                if ($measurements)
                {
                    foreach ($measurements as $measurement)
                    {
                        $points = $cMeasurement->getPointsByUserID($measurement->MeasurementID, $userID);
                        $measurementName = $measurement->Name;

                        if ($cQuestionList->isComplete($measurement->MeasurementID, $measurement->QuestionlistID, $userID))
                        {
                            $aParams[] = array("measurement" => $measurementName, "points" => $points);
                        }
                    }
                }
            }
        }
        
        return $aParams;
    }

}
