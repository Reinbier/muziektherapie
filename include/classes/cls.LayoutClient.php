<?php

/**
 * @author: Reinier Gombert, Patrick Pieper
 * @date: 5-dec-2016
 * 
 * This class will handle the layout for the client
 */
class LayoutClient extends Layout
{

    private $page;
    private $title;
    private $userID;

    public function __construct($userID)
    {
        parent::__construct();
        $this->userID = $userID;
    }

    /**
     * Build the page with the given content
     * 
     * @param type $content
     * @param type $sidebar
     * @return type
     */
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

    /**
     * Set a sidebar
     * 
     * @return string
     */
    private function getLeftSideBar()
    {
        return '
        <div class="panel panel-default">
            <div class="panel-heading">
                <h3 class="panel-title">Snel naar:</h3>
            </div>
            <div class="panel-body">
                <p>
                    <a href="/voortgang/" class="btn btn-success">Mijn voortgang</a>
                </p>
                <p>
                    <a href="/vragenlijsten/" class="btn btn-warning">Vragenlijsten</a>
                </p>
            </div>
        </div>
        ';
    }

    /**
     * Override the header
     * 
     * @return string
     */
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
                        <li ' . ($this->page == "home" ? 'class="active"' : '') . '><a href="/home/">Home</a></li>
                        <li ' . ($this->page == "voortgang" ? 'class="active"' : '') . '><a href="/voortgang/">Voortgang</a></li>
                        <li ' . ($this->page == "vragenlijsten" ? 'class="active"' : '') . '><a href="/vragenlijsten/">Vragenlijsten</a></li>
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

    /**
     * Show the questionlist page or the given subpage
     * 
     * @param type $subpage
     * @param type $subsubpage
     * @return type
     */
    public function getQuestionListPage($subpage = null, $subsubpage = null)
    {
        if (is_numeric($subpage))
        {
            return $this->getQuestionListDetailsPage($subpage, $subsubpage);
        }
        else
        {
            switch ($subpage)
            {
                case "overzicht":
                default:
                    return $this->getQuestionListOverviewPage();
            }
        }
    }

    /**
     * Displays the homepage
     * 
     * @return type
     */
    public function getHomePage()
    {
        $this->page = "home";
        $this->title = "Startpagina";

        $cUser = new User();
        $userData = $cUser->getUserById($this->userID);

        // get completion dates of completed questionlists
        $cQuestionList = new QuestionList();
        $registries = $cQuestionList->getUserLog($this->userID);

        // check if there are logs found
        if (!is_null($registries))
        {
            $message = '<ul class="list-group">';
            // set a limit for the amount of lines showed to the user
            $limit = 10;
            foreach ($registries as $log)
            {
                if ($limit == 0)
                { // when limit reaches 0, break this loop
                    break;
                }
                if ($log["finished"] == true)
                {
                    $message .= "
                        <li class='list-group-item'>
                            <span class='badge'>Aantal punten: " . $log["points"] . "</span>
                            U heeft de vragenlijst <a href='/vragenlijsten/" . $log["MM_ID"] . "/" . $log["QL_ID"] . "/'>" . $log["QL_Name"] . "</a> afgerond op 
                            <span class='text-info'>" . NederlandseDatumTijd($log["date"]) . "</span>
                        </li>";
                }
                else
                {
                    $message .= "
                        <li class='list-group-item'>
                            De vragenlijst <a href='/vragenlijsten/" . $log["MM_ID"] . "/" . $log["QL_ID"] . "/'>" . $log["QL_Name"] . "</a> is nog niet ingevuld.
                        </li>";
                }
                // decrement limit
                $limit--;
            }
            $message .= '</ul>';
        }
        else
        {
            $message = '<p class="lead">Op dit moment geen meldingen om weer te geven</p>';
        }

        $content = '
            <div class="well">
                <h1>Welkom ' . $userData->Name . '</h1>
                <h4>Meldingen</h4>
                ' . $message . '
            </div>
        ';

        return $this->buildPage($content);
    }

    /**
     * Display the progress page for the user
     * 
     * @return type
     */
    public function getProgressPage()
    {
        $cTreatment = new Treatment();
        $output = "";
        // get active treatment
        $treatment = $cTreatment->getActiveTreatmentByUserID($this->userID);

        // if it exists, continue
        if ($treatment)
        {
            $treatmentID = $treatment->TreatmentID;
            $measurements = $cTreatment->getMeasurementsbyTreatmentID($treatmentID);

            // if it contains measurements, print them on screen
            if ($measurements)
            {
                $cQuestionlist = new QuestionList();
                $cMeasurement = new Measurement();
                foreach ($measurements as $measurement)
                {
                    $questionListID = $cQuestionlist->getQuestionListIDByMeasurementID($measurement->MeasurementID);

                    $points = $cMeasurement->getPointsByUserID($measurement->MeasurementID, $this->userID);
                    $output .= "
                        <div class='col-md-6'>
                            <div class='well text-center'>
                                <p class='points'>" . (!$cQuestionlist->isComplete($measurement->MeasurementID, $questionListID, $this->userID) ? "n.t.b." : $points) . "</p>
                                " . $measurement->Name . "
                            </div>
                        </div> ";
                }
            }
        }
        if ($output == "") // no measurements found
        {
            $treatmentID = "0";
            $output = "<div class='col-md-12'>Geen metingen gevonden voor deze behandeling</div>";
        }

        // show page
        $this->page = "voortgang";
        $this->title = "Overzicht eigen gegevens";
        $content = '
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-default">
                    <div class="panel-heading">Metingen binnen deze behandeling
                    </div>
                    <div class="panel-body">
                        <div class="row">
                        ' . $output . '
                        </div>
                    </div>
                </div>


            </div>
            <div class="col-md-8">
                <div id="progressChart" data-treatmentid="' . $treatmentID . '" data-role="client" style="height: 40rem;">

                </div>
            </div>
        </div>';

        return $this->buildPage($content, false);
    }

    /**
     * Show the overview of questionlists
     * 
     * @return type
     */
    private function getQuestionListOverviewPage()
    {
        $this->page = "vragenlijsten";
        $this->title = "Vragenlijsten";
        $cTreatment = new Treatment();
        $treatment = $cTreatment->getActiveTreatmentByUserID($this->userID);
        // show active treatment
        if ($treatment)
        {
            // get measurements
            $measurements = $cTreatment->getMeasurementsbyTreatmentID($treatment->TreatmentID);
            $cQuestionList = new QuestionList();

            // if there are no measurements
            if (!$measurements)
            {
                $output = "Geen vragenlijsten die nog open staan";
            }
            else
            {
                // show measurements
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
        else
        {
            $output = "Geen vragenlijsten gevonden."; // no questionlists
        }

        $content = '
        <div class="row">
            <div class="col-md-12">
                ' . $output . ' 
            </div>
        </div>';

        return $this->buildPage($content, false);
    }

    /**
     * Show a questionlist in full and for the user to fill in.
     * 
     * @param int $measurementID
     * @param int $questionListID
     * @return type
     */
    private function getQuestionListDetailsPage($measurementID, $questionListID)
    {
        // set page vars
        $userID = $this->userID;
        $cQuestionList = new QuestionList();
        $questionListName = $cQuestionList->getQuestionListNameByID($questionListID);

        $this->page = "vragenlijsten";
        $this->title = $questionListName;
        $this->breadcrumbs = array("Vragenlijsten" => "vragenlijsten", $questionListName => "");
        $treatmentCheck = $cQuestionList->checkTreatment($this->userID, $questionListID, $measurementID);

        // if this user has the wrong questionlist
        if (is_null($treatmentCheck))
        {
            return $this->getQuestionListOverviewPage(); // return to his/her overview
        }
        else
        {
            // set initial vars
            $formbody = "Geen vragen gevonden";
            $cQuestion = new Question();
            $cForminputs = new FormInputs();
            $cForminputs->setLabelWidth(1);
            $cForminputs->setInputWidth(11);
            $cForminputs->disableMandatoryNotification();
            $measurementID = $treatmentCheck->MeasurementID;

            // display questions
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
                    // multiple choice question
                    if ($cQuestion->isMultipleChoice($question->QuestionID))
                    {
                        $pos_answers = $cQuestion->getPossibleAnswers($question->QuestionID);
                        if (!empty($pos_answers))
                        {
                            // check if the user already filled in this question
                            $selectedAnswer = $cQuestion->getSelectedAnswer($measurementID, $question->QuestionID, $userID);
                            $selected = "";
                            $selectedID = null;
                            if (!is_null($selectedAnswer)) // set vars for prefilled answer
                            {
                                $selected = $selectedAnswer->PossibleAnswerID;
                                $selectedID = $selectedAnswer->AnswerID;
                            } 
                            // get possible answers for this multiple choice
                            $aAnswers = array();
                            foreach ($pos_answers as $pos_answer)
                            {
                                $aAnswers[$pos_answer->PossibleID] = $pos_answer->Answer;
                            }
                            // add question to the form
                            $cForminputs->addMultipleChoiceQuestion($question->QuestionID, $question->Question, $aAnswers, $selected, $selectedID, $disabled);
                        }
                    }
                    else // open question
                    {
                        $answer = $cQuestion->getAnswer($measurementID, $question->QuestionID, $userID);
                        $selected = "";
                        $selectedID = null;
                        // check if user already provided an answer
                        if (!is_null($answer))
                        {
                            $selected = $answer->Answer;
                            $selectedID = $answer->AnswerID;
                        }
                        // add open question to the form
                        $cForminputs->addOpenQuestion($question->QuestionID, $question->Question, $selected, $selectedID, $disabled);
                    }
                }
                if (!($cQuestionList->isComplete($measurementID, $questionListID, $userID)))
                {
                    $cForminputs->addButton("fillInQuestionList", "Opslaan");
                }

                $formbody = $cForminputs->createFormBody();
            }

            // show questionlist
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
