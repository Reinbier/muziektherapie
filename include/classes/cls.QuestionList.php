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
        
        
        /**
         * Creats a questionlist based on a form, and inserts thes values 
         * into the database
         * @param type $Name
         * 
         */
	public function createQuestionList($Name)
	{
		$sql = "INSERT INTO QuestionList (Name)
				VALUES (:name)";

		$result = $this->query($sql, array(
			":name" => array($Name, PDO::PARAM_STR),
			)
		);
                
                if(!is_null($result))
                {
                    $vragen = array($_POST['vraag'] => array($_POST['points'] => $_POST['answer']));
                    
                    $sqlvraag = "INSERT INTO QUESTION (Question, QuestionlistID)
                                VALUES (:question, :questionlistid)";
                                
                                
                    $sqlanswers = "INSERT INTO POSSIBLE_ANSWER (Points, Answer, QuestionID)
                                VALUES (:points, :answer, :questionid)";
                                  
                        foreach($vragen as $vraag => $answers)
                        {
                           $vraagid = $this->query($sqlvraag, array(
                                    ":question" => array($vraag, PDO::PARAM_STR),
                                    ":questionlistid" => array($result, PDO::PARAM_INT)
                                    ));
                            
                            foreach ($answers as $points => $answer)
                            {
                               $answerid = $this->query($sqlanswers, array(
                                    ":points" => array($points, PDO::PARAM_INT),
                                    ":answer" => array($answer, PDO::PARAM_STR),
                                    ":questionid" => array($vraagid, PDO::PARAM_INT)
                                    ));         
                            }
                        }
                }
                echo "succes";
	}
        
        /**
         * returns a lsit of question from the slected questionlist
         * @param type $questionListID
         * @return type
         */
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
<<<<<<< HEAD
        
        
}	

=======
>>>>>>> master

        public function getAllQuestionlists($questionlistID)
        {
         $sql = "SELECT name
                 FROM  Questionlist
                 WHERE QuestionlistID = :questionlistID";
         
         $result = $this->query($sql, array (
             ":questionlistID" => array($questionlistID, PDO::PARAM_INT)
         ));
            return $result;
        }
}
?>