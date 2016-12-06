<?php
/**
* 
*/
class QuestionList extends DAL
{
	
	function __construct()
	{
		parent::__construct();
	}

	public function createQuestionList($Name)
	{
		$sql = "INSERT INTO QuestionList (Name)
				VALUES (:name)";

		$result = $this->query($sql, array(
			":name" => array($Name, PDO::PARAM_STR),
			),
		);
	}

	public function getQuestions($questionListID)
	{
		$sql = "SELECT *
				FROM Question
				WHERE QuestionlistID = :questionlistID";

		$result = $this->query($sql, array(
			":questionlistID" => array($questionlistID, PDO::PARAM_INT),
			)
		);

		return $result;
	}
}	


?>