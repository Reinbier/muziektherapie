<?php

/*
 * Name: Ronald van der Weide
 * Date: 24/11/2016
 */

Class Answer extends DAL {

    public function __construct() {
        parent::__construct();
    }

    /**
     * Method for retrieving question data
     * 
     * @param int $questionID
     * @return object
     */
    public function getAnswer($answerID) {
        $sql = "SELECT *
                FROM QUESTION
                WHERE QuestionID = :answerid";
        $result = $this->query($sql, array(
            ":answerid" => array($answerID, PDO::PARAM_INT)
                ), "one");

        return $result;
    }

   
    
    public function possibleAnswer()
    {
        $sql = "INSERT INTO ANSWER (PossibleAnswerID, QuestionID,MeasurementID,UserID,Answer)
                VALUES (:possibleanswer,:questionid,:measurementid,:userid,:answer)
            ";
        $possibleanswer = $_POST['possibleanswer'];
        $questionid = $_POST['questionid'];
        $measurementid = $_POST['measurementid'];
        $userid = $_POST['userid'];
        $answer = $_POST['answer'];
        
        $insert = $this->query ($sql, array(":possibleanswer" => array($possibleanswer, PDO::PARAM_INT, "multiiple"),
            ":questionid" => array($questionid, PDO::PARAM_INT, "multiiple"),
            ":measurementid" => array($measurementid, PDO::PARAM_INT, "multiiple"),
            ":userid" => array($userid, PDO::PARAM_INT, "multiiple"),
            ":answer" => array($answer, PDO::PARAM_STR, "multiiple")
            ));
            foreach($insert as $answer)
            {
             $a = array(':possibleanswer'=> $answer = [$possibleanswer],
                        ':questionid' =>$answer = [$possibleanswer],
                        ':measurementid' =>$answer[$measurementid],
                        ':userid' =>$answer[$userid],
                        ':answer' =>$answer[$answer]
                 );
             
                 if ($insert->execute($a)) 
                    {          
                     // Query succeeded.
                     echo "succes";
                    } 
                else 
                {
                    // Query failed.
                 echo $insert->errorCode();
                }
             
            }
        
    }
    
    public function getPoints()
    {
        $sql = "SELECT Points
                FROM POSSIBLE_ANSWER a, ANSWER b
                WHERE a.PossibleAnswerID = b.PossibleAnswerID
                AND a.PossibleAnswerID = :possibleanswerid
                AND b.PossibleAnswerID = :possibleanswerid";
        $result = $this->query($sql, array(
            ":possibleanswerid" => array($this->answerID, PDO::PARAM_INT)
        ));

        if (!is_null($result))
        {
            return true;
        }
        return false;
    }

    public function checkAnswers($UserID, $measurementid)
    {
        $sql = "SELECT * 
                FROM ANSWER
                WHERE UserID = :userid
                AND MeasurementID = :measurementid";

        $answers = $this->query($sql, array(
            ":userid" => array($UserID, PDO::PARAM_INT),
            ":measurementid" => array($measurementid, PDO::PARAM_INT),
            )
        );

        if ($answers)
        {
            foreach ($answers as $answer)
            {
                if (!$answer->PossibleAnswerID && !$answer->Answer) {
                    return false;
                }
                
            }
            return true;
        }
    }
}

?>
