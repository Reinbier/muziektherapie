<?php

class Role extends DAL
{
	
	function __construct()
	{
		parent::__construct();
	}

	/**
	 * Creates a new Role
	 * @param  String $name 	Name of the Role
	 * @return String	       	Returns a string to let user know if it succeeded.
	 */
	public function createRole($name)
	{
		$sql = "INSERT INTO ROLE (name)
				VALUES (:name)";

		$result = $this->query($sql, array(
			":name" => array($name, PDO::PARAM_STR)));

		if (!$result) 
		{
		 	$string = "Something went wrong";
		}
		else 
		{
			$string = "Role succesfully added";
		}
		return $string;
	}

	/**
	 * Gets the role of the added userID
	 * @param  int $userID	  ID of the user from who we will check the role.
	 * @return String         returns the name of the role.
	 */
	public function getRoleByUserID($userID)
	{
		$sql = "SELECT b.Role_name
				FROM USER_ROLE a, ROLE b
				WHERE a.UserID = :userid";

		$result = $this->query($sql, array(
			":userid" => array(
				$userID, PDO::PARAM_INT)), "column");

		return $result;
	}
}