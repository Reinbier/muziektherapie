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
     * Creates a questionlist and returns its inserted ID.
     * 
     * @param String $Name  Name of the question list
     * @return int          QuestionListID
     * 
     */
    public function createQuestionList($Name)
    {
        $sql = "INSERT INTO QUESTIONLIST (Name)
                VALUES (:name)";

        return $this->query($sql, array(
                    ":name" => array($Name, PDO::PARAM_STR)
                        )
        );
    }

    /**
     * returns a list of questions from the selected questionlist
     * @param type $questionListID
     * @return type
     */
    public function getQuestions($questionListID)
    {
        $sql = "SELECT *
                FROM QUESTION
                WHERE QuestionlistID = :questionlistID";

        $result = $this->query($sql, array(
            ":questionlistID" => array($questionListID, PDO::PARAM_INT)
                )
        );

        return $result;
    }

    public function getQuestionListNameByID($questionlistID)
    {
        $sql = "SELECT Name
                FROM QUESTIONLIST
                WHERE QuestionlistID = :questionListID";

        $list = $this->query($sql, array(
            ":questionListID" => array($questionlistID, PDO::PARAM_INT)
                ), "column");

        return $list;
    }

    public function getAllQuestionLists()
    {
        $sql = "SELECT *
                FROM QUESTIONLIST";
        return $this->query($sql);
    }

    public function getQuestionListIDByMeasurementID($measurementID)
    {
        $query = "SELECT QuestionlistID
                FROM MEASUREMENT
                WHERE MeasurementID = :measurementID";

        $result = $this->query($query, array(
            "measurementID" => array($measurementID, PDO::PARAM_INT)
                ), "column");

        return $result;
    }

    public function isComplete($questionlistID, $userID)
    {
        $complete = true;
        $questions = $this->getQuestions($questionlistID);
        if ($questions)
        {
            $cQuestion = new Question();
            foreach ($questions as $question)
            {
                if ($cQuestion->isMultipleChoice($question->QuestionID))
                {
                    if (is_null($cQuestion->getSelectedAnswer($question->QuestionID, $userID)))
                    {
                        $complete = false;
                    }
                }
                else
                {
                    if (is_null($cQuestion->getAnswer($question->QuestionID, $userID)))
                    {
                        $complete = false;
                    }
                }
            }
        }
        else
        {
            $complete = false;
        }

        return $complete;
    }

    public function getCompletionDate($questionListID, $userID)
    {
        // get al questions
        $questions = $this->getQuestions($questionListID);

        if ($questions)
        {
            //prepare array
            $aDates = array();
            $cQuestion = new Question();
            foreach ($questions as $question)
            {
                if ($cQuestion->isMultipleChoice($question->QuestionID))
                {
                    $answer = is_null($cQuestion->getSelectedAnswer($question->QuestionID, $userID));
                }
                else
                {
                    $answer = is_null($cQuestion->getAnswer($question->QuestionID, $userID));
                }
                // add date of answered to array
                $aDates[] = $answer->Date;
            }

            // get max date
            $date = max(array_map('strtotime', $aDates));
        }
        else
        {
            $date = false;
        }

        // return the max date or false
        return $date;
    }

    public function getLogRegistry($therapistID)
    {
        $cTreatment = new Treatment();
        $cUser = new User();

        $subjects = $cUser->getAllSubjects($therapistID);

        $return = null;
        // if there are none
        if (!is_null($subjects))
        {
            $aLogs = array();
            foreach ($subjects as $subject)
            {
                $userID = $subject->UserID;
                $treatmentID = $subject->TreatmentID;
                $treatmentData = $cTreatment->getTreatmentByTreatmentID($treatmentID);
                $treatmentName = $treatmentData->Name;

                $measurements = $cTreatment->getMeasurementsbyTreatmentID($treatmentID);

                if (!is_null($measurements))
                {
                    foreach ($measurements as $measurement)
                    {
                        $questionlistID = $measurement->QuestionlistID;

                        if ($this->isComplete($questionlistID))
                        {
                            $dateQLComplete = $this->getCompletionDate($questionlistID, $userID);
                            $questionlistName = $this->getQuestionListNameByID($questionlistID);
                            $measurementName = $measurement->Name;
                            
                            $userName = $cUser->getUserById($userID)->Name;

                            $aLogs[] = array("date" => $dateQLComplete,
                                            "QL_Name" => $questionlistName,
                                            "MM_Name" => $measurementName,
                                            "TMT_Name" => $treatmentName,
                                            "userName" => $userName);
                        }
                    }
                }
            }

            // sort by a defined function
            usort($aLogs, function($a1, $a2) {
                $v1 = strtotime($a1['date']);
                $v2 = strtotime($a2['date']);
                return $v2 - $v1; // $v2 - $v1 to reverse direction
            });
            
            // return the array
            $return = $aLogs;
        }
        return $return;
    }

    public function checkTreatment($userID, $questionListID)
    {
        $sql = "SELECT *
                FROM TREATMENT a, TREATMENT_USER b, MEASUREMENT c
                WHERE a.TreatmentID = c.TreatmentID
                AND a.TreatmentID = b.TreatmentID
                AND b.UserID = :userID
                AND c.QuestionListID = :qlID";

        $result = $this->query($sql, array(
            ":userID" => array(
                $userID,
                PDO::PARAM_INT
            ),
            "qlID" => array(
                $questionListID,
                PDO::PARAM_INT
            )
                )
        );

        return $result;
    }

}
