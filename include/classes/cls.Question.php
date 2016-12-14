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

   /**
     * Method for creating questionlist
     * 
     * fields are saved as a questionlist
     */

}

?>
