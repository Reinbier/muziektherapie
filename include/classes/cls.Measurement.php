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

	public function getMeasurement(measurementID) 
	{
		$sql = "SELECT *
				FROM MEASUREMENT
				WHERE MeasurementID = measurementID";

	}
}

