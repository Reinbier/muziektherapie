<?php

class Treatment extends DAL
{
	function __construct()
	{
		parent::__construct();
	}

	public function createTreatment($name)
	{
		$sql = "INSERT INTO TREATMENT (Name)
				VALUES (:name)";

		$result = $this->query( $sql, array(
			":name" => array($name, PDO::PARAM_STRING)));

		return $result;
	}

}