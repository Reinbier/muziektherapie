<?php

/**
 * 
 */
class Question extends DAL
{
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function createQuestion($question, $questionListID)
	{
		$sql = 'INSERT INTO QUESTION (Question, QuestionlistID)
				VALUES (:question, :questionListID)';

		$result = $this->query($sql, array(
			":question" => array($question, PDO::PARAM_STR)),
			":questionListID" => array($questionListID, PDO::PARAM_INT),
		);

		return $result;	
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
