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
		$sql = "SELECT *
				FROM TREATMENT_USER a, TREATMENT b
				WHERE a.UserID = :userID
				AND a.TreatmentID = b.TreatmentID
				AND b.Active = 1";

		$result = $this->query($sql, array(
			":userID" => array($userID, PDO::PARAM_INT)),
			"one");

		return $result;
	}

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

    public function drawGraph($userID, $roleName)
    {
        $cQuestionList = new QuestionList();
        $cUser = new User();

        $cTreatment = new Treatment();

        $cMeasurement = new Measurement();
        $aParams = array();

        if ($roleName === 'Therapeut') {
            $treatment = $cTreatment->getTreatmentByUserID($userID);

            $measurements = $cTreatment->getMeasurementsbyTreatmentID($treatment->TreatmentID);

            if($measurements)
            {
               foreach($measurements as $measurement)
               {
                   $users = $cUser->getUsersByTreatmentID($treatment->TreatmentID);
                   $name = $measurement->Name;
                    $aParams = array();
                   foreach ($users as $user)
                   {
                       if($cQuestionList->ísComplete($measurement->QuestionlistID))
                       {
                           $points = $cMeasurement->getPointsByUserID($measurement->MeasurementID, $user->UserID);
                           $aParams[$user->Name] = $points;
                       }
                   }
                   $aParams["measurement"] = $name;
               }

            }
        }
        else {

            $treatment = $cTreatment->getTreatmentByUserID($userID);

            $measurements = $cTreatment->getMeasurementsbyTreatmentID($treatment->TreatmentID);

            if ($measurements) {
                foreach ($measurements as $measurement) {
                    $points = $cMeasurement->getPointsByUserID($measurement->MeasurementID, $userID);
                    $name = $measurement->Name;

                    if($cQuestionList->isComplete($measurement->QuestionlistID))
                    {
                        $aParams[] = array("measurement" => $name, "points" => $points);
                    }
                }
            }
        }
        return $aParams;
    }

}