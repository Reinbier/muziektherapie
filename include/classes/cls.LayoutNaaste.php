<?php

/**
 * @author: Reinier Gombert, Ronald van der Weide
 * @date: 5-dec-2016
 */
class LayoutNaaste extends Layout
{

    private $page;
    private $title;
    private $userID;

    public function __construct($userID)
    {
        parent::__construct();
        $this->userID = $userID;
    }

    private function buildPage($content = "No content found..", $sidebar = true)
    {
        $breadcrumbs = $this->getBreadcrumbs();
        if ($sidebar)
        {
            // build the page with the sidebar
            $return = '
            <div class="row">
                <div class="col-md-12 lead">
                    <h2>' . $this->title . '</h2>
                </div>
            </div>
            ' . $breadcrumbs . ' 
            <div class="row">

                <div class="col-md-3">
                    ' . $this->getLeftSideBar() . '
                </div>

                <div class="col-md-9">
                    ' . $content . '
                </div>

            </div>
            ';
        }
        else
        {
            // build the page without the sidebar
            $return = '
            <div class="row">
                <div class="col-md-12 lead">
                    <h2>' . $this->title . '</h2>
                </div>
            </div> 
            ' . $breadcrumbs . '  
            <div class="row">

                <div class="col-md-12">
                    ' . $content . '
                </div>

            </div>
            ';
        }

        // wrap header, content divs and footer around the content and return it
        return $this->getHeader() . parent::getContent($return) . $this->getFooter();
    }

    private function getLeftSideBar()
    {
        return '
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Meteen naar:</h3>
            </div>
            <div class="panel-body">
                <a href="metingstarten.html" class="btn btn-success">Nieuwe meting</a>
            </div>
        </div>
        ';
    }

    public function getHeader()
    {
        $header = parent::getHeader();

        $cUser = new User();
        $userData = $cUser->getUserById($this->userID);

        $header .= '
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target=".navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                        <a class="navbar-brand" href="#"></a>
                    </div>

                    <div class="collapse navbar-collapse">
                        <ul class="nav navbar-nav">
                            <li class="active"><a href="/home/">Home</a></li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <li class="navbar-text">Ingelogd als ' . $userData->Name . '</li>
                            <li><a href="/?logout">Uitloggen <span class="sr-only">(current)</span></a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        ';
        return $header;
    }

    public function getQuestionListPage($measurementID, $questionListID)
    {
        if (!is_null($questionListID))
        {
            return $this->getQuestionListDetailsPage($measurementID, $questionListID);
        }
        else
        {
            // set page vars
            $this->page = "home";
            $this->title = "Vragenlijsten";

            $cTreatment = new Treatment();
            $treatment = $cTreatment->getActiveTreatmentByUserID($this->userID);

            $output = "Geen vragenlijsten die nog open staan";

            if ($treatment)
            {
                $measurements = $cTreatment->getMeasurementsbyTreatmentID($treatment->TreatmentID);
                $cQuestionList = new QuestionList();

                if ($measurements)
                {
                    $output = '';
                    foreach ($measurements as $measurement)
                    {
                        $questionlistID = $measurement->QuestionlistID;
                        $questionlistName = $cQuestionList->getQuestionListNameByID($questionlistID);

                        if ($cQuestionList->isComplete($measurement->MeasurementID, $questionlistID, $this->userID))
                        {
                            $labelComplete = '<div class="label label-success">Volledig ingevuld</div>';
                        }
                        else
                        {
                            $labelComplete = '<div class="label label-warning">Nog niet volledig ingevuld</div>';
                        }

                        $output .=
                                '<div class="col-md-4">
                                    <div class="well">
                                        ' . $labelComplete . '
                                        <h2> ' . $measurement->Name . '</h2>
                                        <p class="lead">' . $questionlistName . '</p>
                                        <a href="/vragenlijsten/' . $measurement->MeasurementID . '/' . $questionlistID . '/" class="btn btn-primary">Naar vragenlijst</a>
                                     </div>
                                </div>';
                    }
                }
            }

            $content = '
                <div class="row">
                    <div class="col-md-12">
                        ' . $output . ' 
                    </div>
                </div>
            ';

            return $this->buildPage($content, false);
        }
    }

    private function getQuestionListDetailsPage($measurementID, $questionListID)
    {
        $userID = $this->userID;
        $cQuestionList = new QuestionList();
        $questionListName = $cQuestionList->getQuestionListNameByID($questionListID);
        $this->page = "home";
        $this->title = $questionListName;
        $this->breadcrumbs = array("Home" => "home", $questionListName => "");

        $treatmentCheck = $cQuestionList->checkTreatment($this->userID, $questionListID, $measurementID);

        if (is_null($treatmentCheck))
        {
            return $this->getQuestionListOverviewPage();
        }
        else
        {

            $formbody = "Geen vragen gevonden";
            $cQuestion = new Question();
            $cForminputs = new FormInputs();
            $cForminputs->setLabelWidth(1);
            $cForminputs->setInputWidth(11);
            $cForminputs->disableMandatoryNotification();
            $measurementID = $treatmentCheck->MeasurementID;

            $questions = $cQuestionList->getQuestions($questionListID);
            $disabled = "";
            if (!is_null($questions))
            {
                if ($cQuestionList->isComplete($measurementID, $questionListID, $userID))
                {
                    $disabled = "disabled";
                }
                foreach ($questions as $question)
                {
                    if ($cQuestion->isMultipleChoice($question->QuestionID))
                    {
                        $pos_answers = $cQuestion->getPossibleAnswers($question->QuestionID);
                        if (!empty($pos_answers))
                        {
                            $selectedAnswer = $cQuestion->getSelectedAnswer($measurementID, $question->QuestionID, $userID);
                            $selected = "";
                            $selectedID = null;
                            if (!is_null($selectedAnswer))
                            {
                                $selected = $selectedAnswer->PossibleAnswerID;
                                $selectedID = $selectedAnswer->AnswerID;
                            }
                            $aAnswers = array();
                            foreach ($pos_answers as $pos_answer)
                            {
                                $aAnswers[$pos_answer->PossibleID] = $pos_answer->Answer;
                            }
                            $cForminputs->addMultipleChoiceQuestion($question->QuestionID, $question->Question, $aAnswers, $selected, $selectedID, $disabled);
                        }
                    }
                    else
                    {
                        $answer = $cQuestion->getAnswer($measurementID, $question->QuestionID, $userID);
                        $selected = "";
                        $selectedID = null;
                        if (!is_null($answer))
                        {
                            $selected = $answer->Answer;
                            $selectedID = $answer->AnswerID;
                        }
                        $cForminputs->addOpenQuestion($question->QuestionID, $question->Question, $selected, $selectedID, $disabled);
                    }
                }
                if (!($cQuestionList->isComplete($measurementID, $questionListID, $userID)))
                {
                    $cForminputs->addButton("fillInQuestionList", "Opslaan");
                }

                $formbody = $cForminputs->createFormBody();
            }


            $content = '<div class="row">
                <div class="col-md-12">
                    <div class="well">
                        ' . ($disabled == "disabled" ? '<p><span class="label label-success">Deze vragenlijst is afgerond</span></p>' : '') . '
                        <form class="form-horizontal" id="fillInQuestionListForm" data-userid=' . $userID . ' data-measurementid="' . $measurementID . '" onsubmit="return false;">
                            <fieldset>
                            ' . $formbody . '
                            </fieldset>
                        </form>
                    </div>
                </div>
            </div>
            ';

            return $this->buildPage($content, false);
        }
    }

}
