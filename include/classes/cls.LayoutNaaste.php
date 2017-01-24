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

    public function getQuestionListPage($questionListID)
    {
        if (!is_null($questionListID))
        {
            return $this->getQuestionListDetailsPage($questionListID);
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



                        if ($cQuestionList->isComplete($questionlistID, $this->userID))
                        {
                            $output .=
                                    '<div class=" col-md-4">
                                        <div class="well">
                                            <div class="label label-success">
                                                Volledig ingevuld
                                            </div>
                                            <h1> ' . $questionlistName . '</h1>
                                            <p class="lead">' . $measurement->Name . '</p>
                                            <a href="/vragenlijst/' . $questionlistID . '/" class="btn btn-primary">Naar vragenlijst</a>
                                         </div>
                                    </div>';
                        }
                        else
                        {
                            $output .=
                                    '<div class=" col-md-4">
                                        <div class="well">
                                            <div class="label label-warning">
                                                Nog niet volledig ingevuld
                                            </div>
                                            <h1> ' . $questionlistName . '</h1>
                                            <p class="lead">' . $measurement->Name . '</p>
                                            <a href="/vragenlijst/' . $questionlistID . '/" class="btn btn-primary">Naar vragenlijst</a>
                                        </div>
                                    </div>';
                        }
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

    public function getQuestionListDetailsPage($questionListID)
    {
        $cQuestionlist = new QuestionList();
        $questionListName = $cQuestionlist->getQuestionListNameByID($questionListID);
        $this->page = "home";
        $this->title = $questionListName;
        $this->breadcrumbs = array("Home", $questionListName);

        $formbody = "Geen vragen gevonden";
        $cQuestion = new Question();
        $cForminputs = new FormInputs();
        $cForminputs->setLabelWidth(1);
        $cForminputs->setInputWidth(11);
        $cForminputs->disableMandatoryNotification();
        

        $questions = $cQuestionlist->getQuestions($questionListID);
        if ($questions)
        {
            $disabled = "";
            if ($cQuestionlist->isComplete($questionListID, $this->userID))
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
                        $selectedAnswer = $cQuestion->getSelectedAnswer($question->QuestionID, $this->userID);
                        $selected = "";
                        if (!is_null($selectedAnswer)) {
                            $selected = $selectedAnswer->PossibleAnswerID;
                        }
                        $aAnswers = array();
                        foreach ($pos_answers as $pos_answer)
                        {
                            $aAnswers[$pos_answer->PossibleID] = $pos_answer->Answer;
                        }
                        $cForminputs->addMultipleChoiceQuestion($question->QuestionID, $question->Question, $aAnswers, $selected, $disabled);
                    }
                }
                else
                {
                    $answer = $cQuestion->getAnswer($question->QuestionID, $this->userID);
                    $selected = "";
                    if (!is_null($answer)) {
                        $selected = $answer->Answer;
                    }
                    $cForminputs->addOpenQuestion($question->Question, $selected, $disabled);
                }
            }
            if (!($cQuestionlist->isComplete($questionListID, $this->userID)))
            {
                $cForminputs->addButton("fillInQuestionList", "Verzenden");
            }

            $formbody = $cForminputs->createFormBody();
        }

        $content = '<div class="row">
                <div class="col-md-12">
                    <div class="well">
                        <form class="form-horizontal" onsubmit="return false;">
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
