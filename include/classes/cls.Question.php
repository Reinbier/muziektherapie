<?php

/*
 * Name: Ronald van der Weide
 * Date: 24/11/2016
 */

Class Question extends DAL
{

    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Method for retrieving question data
     * 
     * @param int $questionID
     * @return object
     */
    public function getQuestion($questionID)
    {
        $sql = "SELECT *
                FROM QUESTION
                WHERE QuestionID = :questionid";
        $result = $this->query($sql, array(
            ":questionid" => array($questionID, PDO::PARAM_INT)
                ), "one");

        return $result;
    }

    /**
     * Method to check if a question is multiple choice.
     *
     * @param $questionID id for the question to check.
     * @return bool returns true when the question has multiple answers.
     */
    public function isMultipleChoice($questionID)
    {

        $query = "SELECT *
            FROM POSSIBLE_ANSWER
            WHERE QuestionID = :questionID";

        $result = $this->query($query, array(
            ":questionID" => array($questionID, PDO::PARAM_INT),
        ));
        if (count($result) > 0)
        {
            return true;
        }
        else
        {
            return false;
        }
    }

    public function getPossibleAnswers($questionID)
    {
        $query = "SELECT *
            FROM POSSIBLE_ANSWER
            WHERE QuestionID = :questionID";

        $result = $this->query($query, array(
            ":questionID" => array($questionID, PDO::PARAM_INT),
        ));

        return $result;
    }

    public function getSelectedAnswer($questionID)
    {
        $query = "SELECT PossibleAnswerID
                  FROM ANSWER
                  WHERE QuestionID = :questionID";

        $result = $this->query($query, array(
            ":questionID" => array(
                $questionID,
                PDO::PARAM_INT,
            ),
                ), "column");

        return $result;
    }

    public function getAnswer($questionID)
    {
        $query = "SELECT Answer
                  FROM ANSWER
                  WHERE QuestionID = :questionID";

        $result = $this->query($query, array(
            ":questionID" => array(
                $questionID,
                PDO::PARAM_INT,
            ),
                ), "column");

        return $result;
    }

    public function addQuestion($question, $questionListID)
    {
        $sqlInsertQuestion = "INSERT INTO QUESTION (Question, QuestionlistID)
                            VALUES (:question, :questionlistid)";

        return $this->query($sqlInsertQuestion, array(
                    ":question" => array($question, PDO::PARAM_STR),
                    ":questionlistid" => array($questionListID, PDO::PARAM_INT)
        ));
    }

}
