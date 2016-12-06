<?php

class Treatment extends DAL
{
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Creates a new treatment
	 * @param  String $name name of the treatment
	 * @return int        uniquw ID of the inserted treatment.
	 */
	public function createTreatment($name)
	{
		$sql = "INSERT INTO TREATMENT (Name)
				VALUES (:name)";

		$result = $this->query( $sql, array(
			":name" => array($name, PDO::PARAM_STR)));

		return $result;
	}

	/**
	 * Gets treatment where the measurement is linked to.
	 * @param  int $userID 	  The ID of the user which treatment we have to find.
	 * @return [type]         returns the treatment ID of the treatment where user with $userID is patient.
	 */
	public function getTreatmentByUserID($userID)
	{
		$sql = "SELECT TREATMENTID
				FROM TREATMENT_USER
				WHERE UserID = :userID";

		$result = $this->query($sql, array(
			":userID" => array($userID, PDO::PARAM_INT)));

		return $result;
	}

}