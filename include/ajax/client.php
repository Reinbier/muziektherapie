<?php

/**
 * @author: Reinier Gombert, Patrick Pieper
 * @date: 14-dec-2016
 */

require_once ($_SERVER['DOCUMENT_ROOT'] . '/include/config/conf.config.php');

if (isset($_REQUEST['action']))
{
	$userID = $_SESSION['userID'];
	$action = stripslashes(json_decode($_REQUEST['action']));

	if ($action === 'drawGraph')
	{
		$cRole = new Role();

		$cTreatment = new Treatment(); 

		$cMeasurement = new Measurement();

		$roleName = $cRole->getRoleByUserID($userID);

		if ($roleName === 'Therapeut') {
		
		}
		else
		{
			$aParams = array();
			$treatment = $cTreatment->getTreatmentByUserID($userID);

			$measurements = $cTreatment->getMeasurementsbyTreatmentID($treatment->TreatmentID);
	        foreach ($measurements as $measurement)
	        {
            	$points = $cMeasurement->getPointsByUserID($measurement->MeasurementID, $userID);
            	$name = $measurement->Name;

            	$aParams[] = array("measurement" => $name, "points" => $points);
	        }

	        echo json_encode($aParams);			
		}
	}
}