<?php

/**
 * @author: Patrick Pieper
 * @date: 24-nov-2016
 * 
 * Class Measurement
 */
class Measurement extends DAL
{

    function __construct()
    {
        parent::__construct();
    }

    /**
     * Gets a measurent by its ID.
     * @param  int $measurementID ID of the measurement needs to be found.
     * @return bool                Returns if the SELECT is succesfully done.
     */
    public function getMeasurement($measurementID)
    {
        $sql = "SELECT *
		FROM MEASUREMENT
		WHERE MeasurementID = :measurementID";

        $result = $this->query($sql, array(
            ":measurementID" => array($measurementID, PDO::PARAM_INT)), "one");

        return $result;
    }

    /**
     * Create new measurement and add it to database.
     * @param  string $name        name of the measurement.
     * @param  int $treatmentID    id of the treatment that is linked to measurement.
     * @param  int $questionlistID id of the questionlist that needs to be filled in within this measurement.
     * @return int insertedID	   Returns the id of the inserted row. 				                   
     */
    public function createMeasurement($name, $treatmentID, $questionlistID)
    {
        $sql = "INSERT INTO MEASUREMENT (Name, TreatmentID, QuestionlistID) 
		VALUES (:name, :treatmentID, :questionlistID)";

        $result = $this->query($sql, array(
            ":name" => array($name, PDO::PARAM_STR),
            ":treatmentID" => array($treatmentID, PDO::PARAM_INT),
            ":questionlistID" => array($questionlistID, PDO::PARAM_INT)));

        return $result;
    }

    /**
     * Gives a count of the total measurements within a treatment
     * 
     * @param type $treatmentID
     * @return type
     */
    public function getTotalMeasurementsByTreatmentID($treatmentID)
    {
        $sql = "SELECT COUNT(*)
		FROM MEASUREMENT
		WHERE TreatmentID = :treatmentid";

        $result = $this->query($sql, array(
            ":treatmentid" => array($treatmentID, PDO::PARAM_INT)), "column");

        return $result;
    }

    /**
     * Get points for a measurement for a user
     * 
     * @param type $measurementID
     * @param type $userID
     * @return type
     */
    public function getPointsByUserID($measurementID, $userID)
    {
        $measurementID = (int) $measurementID;
        $userID = (int) $userID;

        $sql = "SELECT SUM( Points ) 
                FROM POSSIBLE_ANSWER p, ANSWER a
                WHERE p.PossibleID = a.PossibleAnswerID
                AND a.MeasurementID = :measurementid
                AND a.UserID = :userid";
        $result = $this->query($sql, array(
            ":userid" => array(
                $userID,
                PDO::PARAM_INT),
            ":measurementid" => array(
                $measurementID,
                PDO::PARAM_INT),
                ), "column");
        return $result;
    }
    
    /**
     * Get average points for one measurement
     * 
     * @param type $measurementID
     * @return type
     */
    public function getAveragePointsByMeasurementID($measurementID)
    {
        $measurement = $this->getMeasurement($measurementID);
        $treatmentID = $measurement->TreatmentID;
        
        $cTreatment = new Treatment();
        $allUsers = $cTreatment->getAllUsersInTreatment($treatmentID);
        
        $points = array();
        foreach($allUsers as $user)
        {
            $points[] = $this->getPointsByUserID($measurementID, $user->UserID);
        }
        
        return round(array_sum($points) / count($points));
    }

}
