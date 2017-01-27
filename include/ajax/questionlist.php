<?php

/**
 * @author: Reinier Gombert
 * @date: 16-jan-2017
 */
// include global config file
require_once($_SERVER['DOCUMENT_ROOT'] . '/include/config/conf.config.php');

// get action
if (isset($_REQUEST["action"]))
{
    $action = stripslashes(json_decode($_REQUEST["action"]));

    // create questionlist
    if ($action === "createQuestionList")
    {
        // get parameters
        $questionListParams = json_decode($_REQUEST["questionListParams"], true);

        // set vars
        $qlName = $questionListParams["name"];
        $qlQuestions = $questionListParams["questions"];

        // instantiate necessary classes
        $cQuestionList = new QuestionList();
        $cQuestion = new Question();
        $cAnswer = new Answer();

        // insert Question list
        $questionListID = $cQuestionList->createQuestionList($qlName);

        // check if succesfully created
        if ($questionListID)
        {
            // insert questions
            foreach ($qlQuestions as $aQuestion)
            {
                $question = $aQuestion["question"];
                $aAnswers = $aQuestion["answers"];

                // insert the question
                $questionID = $cQuestion->addQuestion($question, $questionListID);

                // check if succesfully created
                if ($questionID)
                {
                    if (count($aAnswers) > 0)
                    {
                        // insert answers
                        foreach ($aAnswers as $aAnswer)
                        {
                            $answer = $aAnswer["answer"];
                            $points = $aAnswer["points"];

                            // insert answer
                            $cAnswer->addPossibleAnswerToQuestion($questionID, $answer, $points);
                        }
                    }
                }
            }
        }
        // return json to js file
        echo json_encode(
                array(
                    "title" => "Succes!", 
                    "text" => "De vragenlijst is aangemaakt", 
                    "buttons" => "<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>",
                    "lastQuery" => $cAnswer->getLastQuery(),
                    "lastResult" => $cAnswer->getLastQueryResult(),
                    "lastMysqlError" => $cAnswer->getLastMysqlError()
                )
            );
    }
    else if ($action === "fillInQuestionList") // the question list is being filled in
    {
        // get parameters
        $questionListParams = json_decode($_REQUEST["questionListParams"], true);
        // set vars
        $cAnswer = new Answer();
        $measurementID = $questionListParams["measurementID"];
        $userID = $questionListParams["userID"];
        
        // loop through each question with its answer
        foreach($questionListParams["questions"] as $question)
        {
            if(is_null($question["rowid"]))
            {
                // it's a new answer
                $cAnswer->insertAnswer($measurementID, $userID, $question["questionID"], $question["column"], $question["answer"]);
            }
            else
            {
                // it's an existing one and needs update
                $cAnswer->updateAnswer($question["rowid"], $question["column"], $question["answer"]);
            }
        }
        // return json to js file
        echo json_encode(
                array(
                    "title" => "Succes!", 
                    "text" => "Uw antwoorden zijn opgeslagen!", 
                    "buttons" => "<button type='button' class='btn btn-default' data-dismiss='modal' data-reload='yes'>Close</button>",
                    "lastQuery" => $cAnswer->getLastQuery(),
                    "lastResult" => $cAnswer->getLastQueryResult(),
                    "lastMysqlError" => $cAnswer->getLastMysqlError()
                )
            );
    }
}
else
{ // return error
    echo json_encode(
            array(
                "title" => "Aanvraag mislukt",
                "text" => "Er zijn foutieve parameters opgegeven",
                "buttons" => "<button type='button' class='btn btn-default' data-dismiss='modal'>Close</button>"
            )
    );
}