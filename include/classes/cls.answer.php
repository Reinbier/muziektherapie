<?php

/*
 * Name: Ronald van der Weide
 * Date: 24/11/2016
 */

Class Answer extends DAL {

    private $answerID;

    public function __construct($answerID) {
        parent::__construct();

        $this->answerID = $answerID;
    }

    /**
     * Method for retrieving answer data
     * 
     * @param int $answerID
     * @return object
     */
    public function getanswer($answerID) {
        $sql = "SELECT *
                FROM QUESTION
                WHERE QuestionID = :answerid";
        $result = $this->query($sql, array(
            ":answerid" => array($answerID, PDO::PARAM_INT)
                ), "one");

        return $result;
    }

   
   /**
    *  Method for inserting a possible answer into the database,
    *   based on a specific questionid 
    */
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

}

?>
