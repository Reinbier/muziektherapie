<?php

/**
 * @author: Reinier Gombert
 * @date: 5-dec-2016
 */
class LayoutTherapist extends Layout
{

    private $page;
    private $title;
    private $userID;

    public function __construct($userID)
    {
        parent::__construct();
        $this->userID = $userID;
    }

    public function getHeader()
    {
        $return = parent::getHeader();

        $cUser = new User();
        $userData = $cUser->getUserById($this->userID);

        $return .= '
            <nav class="navbar navbar-default">
                <div class="container-fluid">
                    <div class="navbar-header">
                        <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-collapse">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>

                    <div class="collapse navbar-collapse">
                        <ul class="nav navbar-nav">
                            <li' . ($this->page == "home" ? ' class="active"' : '') . '><a href="/home/">Home</a></li>
                            <li class="dropdown' . ($this->page == "client" ? ' active' : '') . '">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Client <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a data-toggle="collapse" data-target=".navbar-collapse" href="/client/aanmaken/">Aanmaken</a></li>
                                    <li><a data-toggle="collapse" data-target=".navbar-collapse" href="/client/overzicht/">Overzicht</a></li>
                                </ul>
                            </li>
                            <li class="dropdown' . ($this->page == "therapeut" ? ' active' : '') . '">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Therapeut <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a data-toggle="collapse" data-target=".navbar-collapse" href="/therapeut/aanmaken/">Aanmaken</a></li>
                                    <li><a data-toggle="collapse" data-target=".navbar-collapse" href="/therapeut/overzicht/">Overzicht</a></li>
                                </ul>
                            </li>
                            <li class="dropdown' . ($this->page == "vragenlijst" ? ' active' : '') . '">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Vragenlijst <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a data-toggle="collapse" data-target=".navbar-collapse" href="/vragenlijst/aanmaken/">Aanmaken</a></li>
                                    <li><a data-toggle="collapse" data-target=".navbar-collapse" href="/vragenlijst/overzicht/">Overzicht</a></li>
                                </ul>
                            </li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <li class="navbar-text">Ingelogd als ' . $userData->Name . '</li>
                            <li><a href="/?logout">Uitloggen</a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        ';
        return $return;
    }

    private function buildPage($content = "No content found..", $sidebar = true)
    {
        $breadcrumbs = $this->getBreadcrumbs();

        if ($sidebar)
        {
            // build the page with the sidebar
            $return = ' 
                <div class="row">
                    <div class="col-md-12 lead"><h2>' . $this->title . '</h2></div>
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
                    <div class="col-md-12 lead"><h2>' . $this->title . '</h2></div>
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
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Meteen naar:</h3>
                </div>
                <div class="panel-body">
                    <a href="metingstarten.html" class="btn btn-success">Nieuwe meting</a>
                </div>
            </div>
        ';
    }

    public function getHomePage()
    {
        $this->page = "home";
        $this->title = "Home";

        $content = '
            <div class="well">
            
                <h3>Meldingen</h3>
                <p>Op dit moment geen meldingen om weer te geven</p>
            
            </div>
        ';

        return $this->buildPage($content);
    }

    public function getTherapistPage($subpage = null)
    {
        if (is_numeric($subpage))
        {
            return $this->getTherapistDetailsPage($subpage);
        }
        else
        {
            switch ($subpage)
            {
                case "aanmaken":
                    return $this->getTherapistCreatePage();
                case "overzicht":
                default:
                    return $this->getTherapistOverviewPage();
            }
        }
    }

    private function getTherapistCreatePage()
    {
        // set page vars
        $this->page = "therapeut";
        $this->title = "Therapeut aanmaken";
        $this->breadcrumbs = array("Therapeut", "Aanmaken");

        $cForm = new FormInputs();
        $cForm->addTextInput("Naam", "Name", true);
        $cForm->addTextInput("Adres", "Address", true);
        $cForm->addTextInput("Woonplaats", "Place", true);
        $cForm->addTextInput("Telefoon", "Phone", true);
        $cForm->addTextInput("Email", "Email", true, "email");
        $cForm->addTextInput("Wachtwoord", "Password", true, "password");
        $cForm->addRadioGroup("Geslacht", "Gender", array("Man", "Vrouw"));
        $cForm->addButton("createTherapist", "Aanmaken");
        $cForm->addResetButton();
        $formBody = $cForm->createFormBody();

        $content = '
            <div class="well">
                <form class="form-horizontal" id="createTherapistForm" onsubmit="return false">
                    <fieldset>
                        <legend>Algemeen</legend>
                        ' . $formBody . '
                    </fieldset>
                </form>
            </div>
        ';

        return $this->buildPage($content);
    }

    private function getTherapistOverviewPage()
    {
        // set page vars
        $this->page = "therapist";
        $this->title = "Therapeut overzicht";
        $this->breadcrumbs = array("Therapeut", "Overzicht");

        $content = '
            <div class="well">
                <table class="table table-striped table-hover dataTable">
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>E-mail</th>
                            <th>Geslacht</th>
                            <th>Plaats</th>
                        </tr>
                    </thead>
        ';
        // fetch all clients from the database
        $cUser = new User();
        $allTherapists = $cUser->getAllTherapists();
        // only begin loop when result is not null
        if (!is_null($allTherapists))
        {
            foreach ($allTherapists as $user) // print info in table
            {
                $content .= "
                        <tr>
                            <td><a href='/therapeut/" . $user->UserID . "/' class='btn btn-link'>" . $user->Name . "</a></td>
                            <td>" . $user->Email . "</td>
                            <td>" . $user->Gender . "</td>
                            <td>" . $user->Place . "</td>
                        </tr>
                ";
            }
        }

        $content .= '
                </table>
            </div>
        ';

        return $this->buildPage($content);
    }

    private function getTherapistDetailsPage($userID)
    {
        $cUser = new User();
        $userDetails = $cUser->getUserById($userID);

        // set page vars
        $this->page = "therapeut";
        $this->title = $userDetails->Name;
        $this->breadcrumbs = array("Therapeut", "Overzicht", $userDetails->Name);


        return $this->buildPage($userDetails->Place, false);
    }

    public function getClientPage($subpage = null)
    {
        if (is_numeric($subpage))
        {
            return $this->getClientDetailsPage($subpage);
        }
        else
        {
            switch ($subpage)
            {
                case "aanmaken":
                    return $this->getClientCreatePage();
                case "overzicht":
                default:
                    return $this->getClientOverviewPage();
            }
        }
    }

    private function getClientCreatePage()
    {
        // set page vars
        $this->page = "client";
        $this->title = "Client aanmaken";
        $this->breadcrumbs = array("Client", "Aanmaken");

        // create new object for the formInputs
        $cForm = new FormInputs();
        // set width of label and inputs
        $cForm->setLabelWidth(3);
        $cForm->setInputWidth(9);

        // add a new section of inputs
        $cForm->addLegend("Algemeen");
        $cForm->addTextInput("Naam", "Name", true);
        $cForm->addTextInput("Adres", "Address", true);
        $cForm->addTextInput("Woonplaats", "Place", true);
        $cForm->addTextInput("Telefoon", "Phone", true);
        $cForm->addTextInput("Mobiel", "Mobile");

        // add a new section of inputs
        $cForm->addLegend("Inloggegevens");
        $cForm->addTextInput("Email", "Email", true, "email");
        $cForm->addTextInput("Wachtwoord", "Password", true, "password");

        // add a new section of inputs
        $cForm->addLegend("Persoonlijk");
        $cForm->addDateInput("Geboortedatum", "Date_of_birth", true);
        $cForm->addRadioGroup("Geslacht", "Gender", array("Man", "Vrouw"));
        $cForm->addRadioGroup("Gehuwd", "Married", array("Ja", "Nee"));
        $cForm->addRadioGroup("Samenwonend", "Cohabiting", array("Ja", "Nee"));

        // add a new section of inputs
        $cForm->addLegend("Opleiding");
        $cForm->addTextInput("Hoogste niveau van opleiding", "Highest_education");
        $cForm->addTextInput("Type opleiding", "Type_of_education");
        $cForm->addTextArea("Werkzaamheden", "Activities");

        // add a new section of inputs
        $cForm->addLegend("Hulpverleners");
        $cForm->addTextInput("Verwijzer", "Referrer");
        $cForm->addTextInput("Huisarts", "Doctor");
        $cForm->addTextInput("Huisartsenpraktijk", "Doctor_practise");
        $cForm->addTextInput("Betrokken behandelaars", "Concerned_therapists");

        // add a new section of inputs
        $cForm->addLegend("Diagnostiek");
        $cForm->addTextArea("Beschreven problematiek", "Issues");
        $cForm->addRadioGroup("Officiele diagnose?", "Official_diagnosed", array("Ja", "Nee"));
        $cForm->addTextInput("Welke professional heeft de diagnose gesteld?", "Who_diagnosed");
        $cForm->addTextInput("Hoe is de diagnose gesteld?", "Way_of_diagnose");
        $cForm->addHelpBlock("Way_of_diagnose", "(interview / vragenlijst/ welke/ anders)");
        $cForm->addTextArea("Aanleiding depressieve klachten", "Occasion_of_depression");
        $cForm->addTextInput("Duur van depressieve klachten", "Length_of_depression");
        $cForm->addRadioGroup("Ernst van depressive klachten", "Severity_of_depression", array("Licht", "Matig", "Ernstig", "Zeer ernstig"));
        $cForm->addTextInput("Aantal keren terugval", "Number_of_recidive");
        $cForm->addTextInput("Lichtgevoeligheid", "Sensitivity");
        $cForm->addHelpBlock("Sensitivity", "(winterdepressie)");
        $cForm->addTextArea("Andere klachten", "Complaints");
        $cForm->addHelpBlock("Complaints", "(comorbiditeit; waaronder lichamelijk)");
        $cForm->addTextArea("Sociaal netwerk", "Social_network");
        $cForm->addHelpBlock("Social_network", "(welke mensen staan je 'nabij')");

        // add a new section of inputs
        $cForm->addLegend("Behandeling");
        $cForm->addTextInput("Doel muziektherapie", "Goal_musictherapy");
        $cForm->addTextInput("Antidepressiva", "Antidepressiva");
        $cForm->addHelpBlock("Antidepressiva", "(ja/nee/naam)");
        $cForm->addTextInput("Andere behandelingen t.b.v. depressieve klachten", "Other_depression_treatment");
        $cForm->addTextInput("Andere medicatie die invloed heeft op depressieve klachten", "Other_medication");
        $cForm->addRadioGroup("Eerder muziektherapeutische behandelingen", "Earlier_musictherapy_treatment", array("Ja", "Nee"));
        $cForm->addTextInput("Muziekervaring", "Music_experience");
        $cForm->addHelpBlock("Music_experience", "(luisteren, maken, alleen, samen, instrument)");
        $cForm->addTextInput("Muzikale voorkeuren", "Musical_preferences");
        $cForm->addHelpBlock("Musical_preferences", "(klassiek, pop, jazz, blues, rock, rap, anders)");
        $cForm->addTextInput("Zet je muziek in om je stemming te beïnvloeden", "Mood_music_usage");
        $cForm->addHelpBlock("Mood_music_usage", "(nooit, soms, vaak)");

        // add a new section of inputs
        $cForm->addLegend("Systematische n=1 methode");
        $cForm->addRadioGroup("Akkoord dat bekenden vragenlijst invullen?", "Allow_relatives", array("Ja", "Nee"));
        $cForm->addTextArea("Gekozen netwerk die vragenlijst gaat invullen", "Chosen_relatives");
        $cForm->addTextArea("Email adressen van dit netwerk", "Email_relatives");

        // add form buttons
        $cForm->addButton("createClient", "Aanmaken");
        $cForm->addResetButton();
        $formBody = $cForm->createFormBody();

        $content = '
            <div class="well">
                <form class="form-horizontal" id="createClientForm" onsubmit="return false">
                    <fieldset>
                        ' . $formBody . '
                    </fieldset>
                </form>
            </div>
        ';

        return $this->buildPage($content);
    }

    private function getClientOverviewPage()
    {
        // set page vars
        $this->page = "client";
        $this->title = "Client overzicht";
        $this->breadcrumbs = array("Client", "Overzicht");

        $content = '
            <div class="well">
                <table class="table table-striped table-hover dataTable">
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>E-mail</th>
                            <th>Geslacht</th>
                            <th>Plaats</th>
                        </tr>
                    </thead>
        ';
        // fetch all clients from the database
        $cUser = new User();
        $allClients = $cUser->getAllClients();
        // only begin loop when result is not null
        if (!is_null($allClients))
        {
            foreach ($allClients as $user) // print info in table
            {
                $content .= "
                        <tr>
                            <td><a href='/client/" . $user->UserID . "/' class='btn btn-link'>" . $user->Name . "</a></td>
                            <td>" . $user->Email . "</td>
                            <td>" . $user->Gender . "</td>
                            <td>" . $user->Place . "</td>
                        </tr>
                ";
            }
        }

        $content .= '
                </table>
            </div>
        ';

        return $this->buildPage($content);
    }

    private function getClientDetailsPage($userID)
    {
        $cUser = new User();
        $userDetails = $cUser->getUserById($userID);

        // set page vars
        $this->page = "client";
        $this->title = $userDetails->Name;
        $this->breadcrumbs = array("Client", "Overzicht", $userDetails->Name);

        $cTreatment = new Treatment();
        $cMeasurement = new Measurement();
        
        $treatment = $cTreatment->getTreatmentByUserID($userID);
        
        $measurements = $cTreatment->getMeasurementsbyTreatmentID($treatment->TreatmentID);

        $output = "";
        if ($measurements)
        {
            $cQuestionlist = new QuestionList();
            foreach ($measurements as $measurement)
            {
                $questionListID = $cQuestionlist->getQuestionListIDByMeasurementID($measurement->MeasurementID);

                $points = $cMeasurement->getPointsByUserID($measurement->MeasurementID, $userID);
                $output .= "
                <div class='col-md-6'>
                    <div class='well text-center'>
                        <p class='points'>" . (!$cQuestionlist->isComplete($questionListID) ? "n.t.b." : $points) . "</p>
                        " . $measurement->Name . "
                    </div>
                </div> ";
            }
        }
        else
        {
            $output = "<div class='col-md-12'>Geen metingen gevonden voor deze behandeling</div>";
        }
        
        $content = '
        <div class="row">
            <div class="col-md-3">
                <div class="list-group">
                    <a href="#" class="list-group-item active">
                        Patrick Pieper
                    </a>
                    <a href="#" class="list-group-item">
                        Sonja Aalbers
                    </a>
                    <a href="#" class="list-group-item">
                        Professional
                    </a>
                </div>
            </div>
            <div class="col-md-9">
                <div id="progressChartOverview" data-userid="' . $userID . '" style="height: 40rem;">

                </div>
            </div>
        </div>';

        return $this->buildPage($content, false);
    }

    public function getQuestionListPage($subpage = null)
    {
        if (is_numeric($subpage))
        {
            return $this->getQuestionListDetailsPage($subpage);
        }
        else
        {
            switch ($subpage)
            {
                case "aanmaken":
                    return $this->getQuestionListCreatePage();
                case "overzicht":
                default:
                    return $this->getQuestionListOverviewPage();
            }
        }
    }

    private function getQuestionListCreatePage()
    {
        // set page vars
        $this->page = "vragenlijst";
        $this->title = "Vragenlijst aanmaken";
        $this->breadcrumbs = array("Vragenlijst", "Aanmaken");

        $cForm = new FormInputs();
        $cForm->addLegend("Vragenlijst details");
        $cForm->addTextInput("Naam", "Name", true);
        $cForm->addLegend("Vragen");
        $formBody = $cForm->createFormBody();

        $content = '
            <div class="well">
                <form class="form-horizontal" id="createQuestionListForm" onsubmit="return false">
                    <fieldset>
                        ' . $formBody . '
                            
                        <div class="questions">
                        
                            <div class="form-group question">
                                <div class="col-lg-12">
                                    <div class="input-group">
                                        <span class="input-group-addon">1.</span>
                                        <input type="text" class="form-control input-question" name="question" required>
                                        <span class="input-group-btn">
                                            <button class="btn btn-danger removeQuestion" type="button">X</button>
                                        </span>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" class="multiple-choice" checked> Meerkeuze
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group answers">
                                <label class="col-lg-2 control-label">Antwoorden</label>
                                <div class="col-lg-10">
                                    <div class="input-group answer">
                                        <span class="input-group-addon">1.</span>
                                        <div class="col-lg-10 nopadding">
                                            <input type="text" class="form-control input-answer" name="answer" required>
                                        </div>
                                        <div class="col-lg-2 nopadding">
                                            <input type="text" class="form-control input-points" name="points" required placeholder="punten">
                                        </div>
                                        <span class="input-group-btn">
                                            <button class="btn btn-danger removeAnswer" type="button">X</button>
                                        </span>
                                    </div>
                                    <button class="btn btn-success addAnswer" type="button"><i class="glyphicon-plus"></i> Antwoord</button>
                                </div>
                            </div>
                            
                        </div>
                        
                        <div class="form-group">
                            <div class="col-lg-12">
                                <a href="javascript:;" class="btn btn-success addQuestion"><i class="glyphicon-plus"></i> Vraag</a>
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <div class="col-lg-12 text-right">
                                <button type="reset" class="btn btn-default btnReset">Reset</button> <button type="submit" class="btn btn-primary" id="button-createQuestionlist" name="button-createQuestionlist">Aanmaken</button>
                            </div>
                        </div>


                    </fieldset>
                </form>
            </div>
        ';

        return $this->buildPage($content, false);
    }

    private function getQuestionListOverviewPage()
    {
        // set page vars
        $this->page = "vragenlijst";
        $this->title = "Vragenlijst overzicht";
        $this->breadcrumbs = array("Vragenlijst", "Overzicht");

        $content = '
            <div class="well">
                <table class="table table-striped table-hover dataTable">
                    <thead>
                        <tr>
                            <th>Naam</th>
                            <th>Aantal vragen</th>
                        </tr>
                    </thead>
        ';
        // fetch all clients from the database
        $cQuestionList = new QuestionList();
        $allQuestionLists = $cQuestionList->getAllQuestionLists();
        // only begin loop when result is not null
        if (!is_null($allQuestionLists))
        {
            foreach ($allQuestionLists as $list) // print info in table
            {
                $questions = $cQuestionList->getQuestions($list->QuestionlistID);
                $content .= "
                        <tr>
                            <td><a href='/vragenlijst/" . $list->QuestionlistID . "/' class='btn btn-link'>" . $list->Name . "</a></td>
                            <td>" . count($questions) . "</td>
                        </tr>
                ";
            }
        }

        $content .= '
                </table>
            </div>
        ';

        return $this->buildPage($content);
    }

}
