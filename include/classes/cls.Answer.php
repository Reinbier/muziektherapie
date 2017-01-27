<?php

/*
 * Name: Ronald van der Weide, Reinier Gombert
 * Date: 24/11/2016
 */

Class Answer extends DAL
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Method for retrieving answer data
     * 
     * @param int $answerID
     * @return object
     */
    public function getAnswer($answerID)
    {
        $sql = "SELECT *
                FROM QUESTION
                WHERE QuestionID = :answerid";
        $result = $this->query($sql, array(
            ":answerid" => array($answerID, PDO::PARAM_INT)
                ), "one");

        return $result;
    }
    
    /**
     * Add multiple choise answer
     * 
     * @param int $questionID
     * @param string $answer
     * @param int $points
     * @return type
     */
    public function addPossibleAnswerToQuestion($questionID, $answer, $points)
    {
        $sqlInsertPossibleAnswer = "INSERT INTO POSSIBLE_ANSWER (QuestionID, Answer, Points)
                        VALUES (:questionid, :answer, :points)";

        return $this->query($sqlInsertPossibleAnswer, array(
                    ":questionid" => array($questionID, PDO::PARAM_INT),
                    ":answer" => array($answer, PDO::PARAM_STR),
                    ":points" => array($points, PDO::PARAM_INT)
        ));
    }

    /**
     * Inserts an answer based on the parameters
     * 
     * @param int $measurementID
     * @param int $userID
     * @param int $questionID
     * @param string $column
     * @param string|int $answer
     * @return type
     */
    public function insertAnswer($measurementID, $userID, $questionID, $column, $answer)
    {
        return $this->insert("ANSWER", array(
            "MeasurementID" => $measurementID,
            "UserID" => $userID,
            "QuestionID" => $questionID,
            $column => $answer
        ));
    }

    /**
     * Updates an answer in the db
     * 
     * @param int $answerID
     * @param string $column
     * @param string|int $answer
     * @return type
     */
    public function updateAnswer($answerID, $column, $answer)
    {
        $sql = "UPDATE ANSWER
                SET " . $column . " = :answer
                WHERE AnswerID = :answerid";
        return $this->query($sql, array(
            ":answer" => array($answer, PDO::PARAM_STR),
            ":answerid" => array($answerID, PDO::PARAM_INT)
        ));
    }

}
