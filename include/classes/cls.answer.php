<?php

/*
 * Name: Ronald van der Weide
 * Date: 24/11/2016
 */

Class Question extends DAL {

    private $questionID;

    public function __construct($answerID) {
        parent::__construct();

        $this->QuestionID = $answerID;
    }

    /**
     * Method for retrieving question data
     * 
     * @param int $questionID
     * @return object
     */
    public function getanswer($questionID) {
        $sql = "SELECT *
                FROM QUESTION
                WHERE QuestionID = :questionid";
        $result = $this->query($sql, array(
            ":questionid" => array($questionID, PDO::PARAM_INT)
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
        
        $insert = $this->query($sql,array(
            foreach($insert as $answer)
            {
             $a = array(':possibleanswer'=> $answer = [$possibleanswer],  PDO::PARAM_INT
                        ':questionid'=>$answer = [$possibleanswer],
                        ':measurementid'=>$answer[$measurementid],
                        ':userid'=>$answer[$userid],
                        ':answer'=>$answer[$answer]
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
        ))
    }

}

?>
