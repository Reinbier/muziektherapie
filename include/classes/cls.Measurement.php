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
	 * @param  int $measurementID [description]
	 * @return [type]                [description]
	 */
	public function getMeasurement($measurementID) 
	{
		$sql = "SELECT *
				FROM MEASUREMENT
				WHERE MeasurementID =".":measurementID";

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
	public function create_Measurement($name, $treatmentID, $questionlistID)
	{
		$sql = "INSERT INTO MEASUREMENT (Number, Name, TreatmentID, QuestionlistID) 
				VALUES (:number, :name, :treatmentID, :questionlistID)";

		$result = $this->query($sql, array(
			":name" => array($name, PDO::PARAM_STRING),
			":treatmentID" => array($treatmentID, PDO::PARAM_INT),
			"questionlistID" => array($questionlistID, PDO::PARAM_INT)));

		return $result;
	}

}

