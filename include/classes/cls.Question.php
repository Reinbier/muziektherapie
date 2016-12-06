<?php

/*
 * Name: Ronald van der Weide
 * Date: 24/11/2016
 */

Class Question extends DAL {

    private $questionID;

    public function __construct($questionID) {
        parent::__construct();

        $this->QuestionID = $questionID;
    }

    /**
     * Method for retrieving question data
     * 
     * @param int $questionID
     * @return object
     */
    public function getQuestion($questionID) {
        $sql = "SELECT *
                FROM QUESTION
                WHERE QuestionID = :questionid";
        $result = $this->query($sql, array(
            ":questionid" => array($questionID, PDO::PARAM_INT)
                ), "one");

        return $result;
    }

    public function showQuestionlist() {
        $sql = "SELECT *
                FROM QUESTION a, QUESTIONLIST b
                WHERE a.QuestionID = b.QuestionID
                AND a.QuestionID = :questionid";

        $result = $this->query($sql, array(
            ":questionid" => array($this->questionID, PDO::PARAM_INT)
                ));

        if (!is_null($result)) {
            return true;
        }
        return false;
    }

    public function createQuestionlist() 
    {
        $sql = "INSERT INTO QUESTIONLIST (QuestionlistID, Name)
        VALUES (:questionlistid, :questionltisname)    
        ";
        
        $result = $this->query($sql, array(
            ":questionlistid" => array ($this->$_POST(['questionlistID']), PDO::PARAM_INT),
            ":questionlistname" => array ($this->$_POST(['questionlistName']), PDO::PARAM_STR)
            ));
        
        if (!is_null($result))
        {
            $questions = "INSERT INTO QUESTION (QuestionID, Question, Type, QuestionlistID)
                          VALUES (:questionid, :question, :type, :questionlistid)
                         ";
            
            $insert = $this->query($questions, array(
            ":questionid" => array ($this->$_POST(['questionID']),$questionid, PDO::PARAM_INT),
            ":question" => array ($this->$_POST(['question']),$question, PDO::PARAM_STR),
            ":type" => array ($this->$_POST(['type']),$type , PDO::PARAM_STR),   
            ":questionlistid" => array ($this->$_POST(['questionlistID']),$questionlistid ,PDO::PARAM_INT), 
            ));
            
            foreach ($insert as $value) 
            {
                 $a = array    (':questionid'=>$value[$questionid],
                                ':question'=>$value[$question],
                                ':type'=>$value[$type],
                                ':questionlistid'=>$value[$questionlistid]);
                 
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
        else
        {
            return false;
        }
        
        
       
    
    }

}

?>
