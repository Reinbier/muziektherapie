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
	 * @return int       		ID of the created Role.
	 */
	public function createRole($name)
	{
		$sql = "INSERT INTO ROLE (name)
				VALUES (:name)";

		$result = $this->query($sql, array(
			":name" => array($name, PDO::PARAM_STR)));

		return $result;
	}

	/**
	 * Gets the role of the added userID
	 * @param  int $userID	  ID of the user from who we will check the role.
	 * @return int         returns the ID of the role.
	 */
	public function getRole($userID)
	{
		$sql = "SELECT RoleID
				FROM USER_ROLE
				WHERE UserID = :userid";

		$result = $this->query($sql, array(
			":userid" => array(
				$userID, PDO::PARAM_INT)));

		return $result;
	}
}