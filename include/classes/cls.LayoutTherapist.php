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
                            <li class="dropdown' . ($this->page == "nieuw" ? ' active' : '') . '">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Nieuw <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="/nieuw/behandeling/">Behandeling</a></li>
                                    <li><a href="/nieuw/meting/">Meting</a></li>
                                    <li><a href="/nieuw/vragenlijst/">Vragenlijst</a></li>
                                    <li class="divider"></li>
                                    <li><a href="/nieuw/client/">Client</a></li>
                                    <li><a href="/nieuw/naaste/">Naaste</a></li>
                                    <li><a href="/nieuw/professional/">Professional</a></li>
                                    <li><a href="/nieuw/therapeut/">Therapeut</a></li>
                                </ul>
                            </li>
                            <li class="dropdown' . ($this->page == "overzicht" ? ' active' : '') . '">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Overzicht <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="/overzicht/behandelingen/">Behandelingen</a></li>
                                    <li><a href="/overzicht/vragenlijsten/">Vragenlijsten</a></li>
                                    <li class="divider"></li>
                                    <li><a href="/overzicht/gebruikers/">Gebruikers</a></li>
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
                    <p>
                        <a href="/nieuw/behandeling/" class="btn btn-warning">Nieuwe behandeling</a>
                    </p>
                    <p>
                        <a href="/nieuw/meting/" class="btn btn-success">Nieuwe meting</a>
                    </p>
                </div>
            </div>
        ';
    }

    public function getHomePage()
    {
        $this->page = "home";
        $this->title = "Home";

        // get completion dates of completed questionlists
        $cQuestionList = new QuestionList();
        $registries = $cQuestionList->getLogRegistry($this->userID);

        // check if there are logs found
        if (!is_null($registries))
        {
            $message = '<ul class="list-group">';
            foreach ($registries as $log)
            {
                $message .= "
                    <li class='list-group-item'>
                        <span class='badge'>Aantal punten: " . $log["points"] . "</span>
                        <a href='/overzicht/gebruikers/" . $log["userID"] . "/'>" . $log["userName"] . "</a> heeft vragenlijst 
                        <a href='/overzicht/vragenlijsten/" . $log["QL_ID"] . "/'>" . $log["QL_Name"] . "</a> afgerond voor meting 
                        <a href='/overzicht/metingen/" . $log["MM_ID"] . "/'>" . $log["MM_Name"] . "</a> van 
                        <a href='/overzicht/behandelingen/" . $log["TMT_ID"] . "/'>" . $log["TMT_Name"] . "</a> op 
                        <span class='text-info'>" . NederlandseDatumTijd($log["date"]) . "</span>
                    </li>";
            }
            $message .= '</ul>';
        }
        else
        {
            $message = '<p>Er zijn geen resultaten gevonden.</p>';
        }

        $content = '
            <div class="well">
            
                <h3>Meldingen</h3>
                ' . $message . '
            
            </div>
        ';

        return $this->buildPage($content);
    }

    public function getNewPage($subpage = null)
    {
        switch ($subpage)
        {
            case "behandeling":
                return $this->getNewTreatmentPage();
            case "meting":
                return $this->getNewMeasurementPage();
            case "vragenlijst":
                return $this->getNewQuestionListPage();
            case "client":
                return $this->getNewClientPage();
            case "naaste":
            case "professional":
                return $this->getNewKinPage($subpage);
            case "therapeut":
                return $this->getNewTherapistPage();
            default:
                return $this->getHomePage();
        }
    }

    private function getNewQuestionListPage()
    {
        // set page vars
        $this->page = "nieuw";
        $this->title = "Vragenlijst aanmaken";
        $this->breadcrumbs = array("Home", "Nieuwe vragenlijst");

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

    private function getNewClientPage()
    {
        // set page vars
        $this->page = "nieuw";
        $this->title = "Client aanmaken";
        $this->breadcrumbs = array("Home", "Nieuwe client");

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

        return $this->buildPage($content, false);
    }

    private function getNewKinPage($roleName)
    {
        // set page vars
        $this->page = "nieuw";
        $this->title = ucfirst($roleName) . " aanmaken";
        $this->breadcrumbs = array("Home", "Nieuwe " . $roleName);

        $cForm = new FormInputs();
        $cForm->addTextInput("Naam", "Name", true);
        $cForm->addTextInput("Adres", "Address");
        $cForm->addTextInput("Woonplaats", "Place");
        $cForm->addTextInput("Telefoon", "Phone");
        $cForm->addTextInput("Email", "Email", true, "email");
        $cForm->addTextInput("Wachtwoord", "Password", true, "password");
        $cForm->addRadioGroup("Geslacht", "Gender", array("Man", "Vrouw"));
        $cForm->addButton("createKin", "Aanmaken");
        $cForm->addResetButton();
        $formBody = $cForm->createFormBody();

        $content = '
            <div class="well">
                <form class="form-horizontal" id="createKinForm" onsubmit="return false">
                    <fieldset>
                        <legend>Algemeen</legend>
                        <span id="roleName" style="display:none">' . ucfirst($roleName) . '</span>
                        ' . $formBody . '
                    </fieldset>
                </form>
            </div>
        ';

        return $this->buildPage($content, false);
    }

    private function getNewTherapistPage()
    {
        // set page vars
        $this->page = "nieuw";
        $this->title = "Therapeut aanmaken";
        $this->breadcrumbs = array("Home", "Nieuwe therapeut");

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

        return $this->buildPage($content, false);
    }

    public function getOverviewPage($subpage = null, $subsubpage = null)
    {
        switch ($subpage)
        {
            case "behandelingen":
                return $this->getOverviewTreatmentsPage($subsubpage);
            case "metingen":
                return $this->getOverviewMeasurementsPage($subsubpage);
            case "vragenlijsten":
                return $this->getOverviewQuestionListsPage($subsubpage);
            case "gebruikers":
                return $this->getOverviewUsersPage($subsubpage);
            default:
                return $this->getHomePage();
        }
    }

    private function getOverviewTreatmentsPage($treatmentID)
    {
        if (!is_null($treatmentID))
        {
            return $this->getOverviewTreatmentsDetailsPage($treatmentID);
        }
        else
        {
            // set page vars
            $this->page = "overzicht";
            $this->title = "Behandelingen overzicht";
            $this->breadcrumbs = array("Overzicht", "Behandelingen");

            $content = '
                <div class="well">
                    <table class="table table-striped table-hover dataTable">
                        <thead>
                            <tr>
                                <th>Naam</th>
                                <th>Client</th>
                                <th>Aantal metingen</th>
                                <th>Overige deelnemers</th>
                            </tr>
                        </thead>
            ';
            // fetch all treatments for this therapist from the database
            $cTreatment = new Treatment();
            $cUser = new User();
            $allTreatments = $cTreatment->getTreatmentsByUserID($this->userID);
            // only begin loop when result is not null
            if (!is_null($allTreatments))
            {
                foreach ($allTreatments as $treatment) // print info in table
                {
                    $treatmentID = $treatment->TreatmentID;

                    $treatmentData = $cTreatment->getTreatmentByTreatmentID($treatmentID);
                    // assemble data we need
                    $measurements = $cTreatment->getMeasurementsbyTreatmentID($treatmentID);
                    $kinPeople = $cTreatment->getKinbyTreatmentID($treatmentID);
                    $client = $cTreatment->getClientIDbyTreatmentID($treatmentID);
                    $clientData = $cUser->getUserById($client->UserID);
                    // create string of kin people to put in the table
                    $sKin = "";
                    if (is_null($kinPeople))
                    {
                        $sKin = "<p class='text-muted'>Geen gebruikers gevonden..</p>";
                    }
                    else
                    {
                        foreach ($kinPeople as $kin)
                        {
                            $kinData = $cUser->getUserById($kin->UserID);
                            $sKin .= ($sKin == "" ? "<a href='/overzicht/gebruikers/" . $kin->UserID . "/'>" . $kinData->Name . "</a> (" . $kin->Role_name . ")" : ", <a href='/overzicht/gebruikers/" . $kin->UserID . "/'>" . $kinData->Name . "</a> (" . $kin->Role_name . ")");
                        }
                    }

                    $content .= "
                            <tr>
                                <td><a href='/overzicht/behandelingen/" . $treatmentID . "/' class='btn btn-link'>" . $treatmentData->Name . "</a></td>
                                <td><a href='/overzicht/gebruikers/" . $client->UserID . "/' class='btn btn-link'>" . $clientData->Name . "</a></td>
                                <td>" . count($measurements) . "</td>
                                <td>" . $sKin . "</td>
                            </tr>
                    ";
                }
            }

            $content .= '
                    </table>
                </div>
            ';

            return $this->buildPage($content, false);
        }
    }

    private function getOverviewTreatmentsDetailsPage($treatmentID)
    {
        $cTreatment = new Treatment();
        $treatmentData = $cTreatment->getTreatmentByTreatmentID($treatmentID);
        // set page vars
        $this->page = "overzicht";
        $this->title = $treatmentData->Name;
        $this->breadcrumbs = array("Overzicht", "Behandelingen", $treatmentData->Name);

        $cMeasurement = new Measurement();
        $cUser = new User();
        $client = $cTreatment->getClientIDbyTreatmentID($treatmentID);
        $clientID = $client->UserID;
        $clientData = $cUser->getUserById($clientID);


        $measurements = $cTreatment->getMeasurementsbyTreatmentID($treatmentID);

        if (!is_null($measurements))
        {
            $output = '<div class="list-group col-md-12">';

            $cQuestionlist = new QuestionList();
            foreach ($measurements as $measurement)
            {
                $questionListID = $cQuestionlist->getQuestionListIDByMeasurementID($measurement->MeasurementID);

                $points = $cMeasurement->getPointsByUserID($measurement->MeasurementID, $clientID);
                $output .= '
                    <a href="/overzicht/metingen/' . $measurement->MeasurementID . '" class="list-group-item">
                        <h4 class="list-group-item-heading">' . $measurement->Name . '</h4>
                        <p class="list-group-item-text"><span class="label label-warning">Gemiddelde aantal punten: ' . $points . '</span></p>
                    </a>
                ';
            }
            $output .= '</div>';
        }
        else
        {
            $output = "<div class='col-md-12'>Geen metingen gevonden voor deze behandeling</div>";
        }

        $allKin = $cUser->getAllUsers(array("Naaste", "Professional"));
        // store kin in options array
        $aKinOptions = array();
        foreach ($allKin as $kin)
        {
            if (!$cTreatment->userInTreatment($kin->UserID, $treatmentID))
            {
                // check if this user not already is in this treatment
                $aKinOptions[] = '<option value=' . $kin->UserID . '>' . $kin->Name . '</option>';
            }
        }

        $content = '
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            <h3 class="panel-title">Metingen binnen deze behandeling</h3>
                        </div>
                        <div class="panel-body">
                            ' . $output . '
                            <div class="col-md-12">
                                <a href="#" class="btn btn-success">Nieuwe meting</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="panel panel-warning">
                        <div class="panel-heading">
                            <h3 class="panel-title">Naasten toevoegen aan behandeling</h3>
                        </div>
                        <div class="panel-body">
        ';
        if (count($aKinOptions) > 0)
        {
            $content .= '
                            <form class="form-horizontal" id="addKinToTreatmentForm" onsubmit="return false">
                                <fieldset>
                                    <div class="form-group">
                                        <div class="col-md-9">
                                            <select class="form-control" name="selectKin" id="selectKin" data-treatmentid="' . $treatmentID . '">
            ';
            foreach ($aKinOptions as $option)
            {
                // check if this user not already is in this treatment
                $content .= $option;
            }
            $content .= '
                                            </select>
                                        </div>
                                        <div class="col-md-3">
                                            <button type="submit" class="btn btn-success" id="button-addKinToTreatment">Toevoegen</button>
                                        </div>
                                    </div>
                                </fieldset>
                            </form>
            ';
        }
        else
        {
            $content .= '<p class="text-muted">Geen naasten beschikbaar..</p>';
        }
        $content .= '
                        </div>
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="panel panel-info">
                        <div class="panel-heading">
                            <h3 class="panel-title">Globale voortgang</h3>
                        </div>
                        <div class="panel-body">
                            <div id="progressChartOverview" data-userid="' . $clientID . '" style="height: 40rem;">

                            </div>
                        </div>
                    </div>
                </div>
            </div>
        ';

        return $this->buildPage($content, false);
    }

    private function getOverviewUsersPage($userID)
    {
        if (!is_null($userID))
        {
            return $this->getOverviewUsersDetailsPage($userID);
        }
        else
        {
            // set page vars
            $this->page = "overzicht";
            $this->title = "Gebruikers overzicht";
            $this->breadcrumbs = array("Overzicht", "Gebruikers");

            $content = '
                <div class="well">
                    <table class="table table-striped table-hover dataTable">
                        <thead>
                            <tr>
                                <th>Naam</th>
                                <th>E-mail</th>
                                <th>Geslacht</th>
                                <th>Plaats</th>
                                <th>Rol</th>
                            </tr>
                        </thead>
            ';
            // fetch all clients from the database
            $cUser = new User();
            $allUsers = $cUser->getAllUsers();
            // only begin loop when result is not null
            if (!is_null($allUsers))
            {
                foreach ($allUsers as $user) // print info in table
                {
                    $cRole = new Role();
                    $roleName = $cRole->getRoleByUserID($user->UserID);

                    $content .= "
                            <tr>
                                <td><a href='/overzicht/gebruikers/" . $user->UserID . "/' class='btn btn-link'>" . $user->Name . "</a></td>
                                <td>" . $user->Email . "</td>
                                <td>" . $user->Gender . "</td>
                                <td>" . $user->Place . "</td>
                                <td>" . $roleName . "</td>
                            </tr>
                    ";
                }
            }

            $content .= '
                    </table>
                </div>
            ';

            return $this->buildPage($content, false);
        }
    }

    private function getOverviewUsersDetailsPage($userID)
    {
        $cUser = new User();
        $userDetails = $cUser->getUserById($userID);

        // set page vars
        $this->page = "overzicht";
        $this->title = $userDetails->Name;
        $this->breadcrumbs = array("Overzicht", "Gebruikers", $userDetails->Name);

        $cTreatment = new Treatment();
        $cMeasurement = new Measurement();

        $output = "";
        $treatment = $cTreatment->getActiveTreatmentByUserID($userID);

        if (!is_null($treatment))
        {
            $measurements = $cTreatment->getMeasurementsbyTreatmentID($treatment->TreatmentID);

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
                                <p class='points'>" . (!$cQuestionlist->isComplete($questionListID, $userID) ? "n.t.b." : $points) . "</p>
                                " . $measurement->Name . "
                            </div>
                        </div> ";
                }
            }
        }
        else
        {
            $output = "<div class='col-md-12'>Geen metingen gevonden voor deze behandeling</div>";
        }

        $content = '
        <div class="row">
            <div class="col-md-4">
                <div class="row">
                    ' . $output . '
                </div>
            </div>
            <div class="col-md-8">
                <div id="progressChartOverview" data-userid="' . $userID . '" style="height: 40rem;">

                </div>
            </div>
        </div>';

        return $this->buildPage($content, false);
    }

    private function getOverviewQuestionListsPage($questionListID)
    {
        if (!is_null($questionListID))
        {
            return $this->getOverviewQuestionListsDetailsPage($questionListID);
        }
        else
        {
            // set page vars
            $this->page = "overzicht";
            $this->title = "Vragenlijst overzicht";
            $this->breadcrumbs = array("Overzicht", "Vragenlijsten");

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
                                <td><a href='/overzicht/vragenlijsten/" . $list->QuestionlistID . "/' class='btn btn-link'>" . $list->Name . "</a></td>
                                <td>" . count($questions) . "</td>
                            </tr>
                    ";
                }
            }

            $content .= '
                    </table>
                </div>
            ';

            return $this->buildPage($content, false);
        }
    }

    public function getOverviewQuestionListsDetailsPage($questionListID)
    {
        $cQuestionlist = new QuestionList();
        $questionListName = $cQuestionlist->getQuestionListNameByID($questionListID);
        $this->page = "overzicht";
        $this->title = $questionListName;
        $this->breadcrumbs = array("Overzicht", "Vragenlijsten", $questionListName);

        $formbody = "Geen vragen gevonden";
        $cQuestion = new Question();
        $cForminputs = new FormInputs();
        $cForminputs->setLabelWidth(1);
        $cForminputs->setInputWidth(11);
        $cForminputs->disableMandatoryNotification();

        $questions = $cQuestionlist->getQuestions($questionListID);
        if ($questions)
        {
            foreach ($questions as $question)
            {
                if ($cQuestion->isMultipleChoice($question->QuestionID))
                {
                    $pos_answers = $cQuestion->getPossibleAnswers($question->QuestionID);
                    if (!empty($pos_answers))
                    {
                        $aAnswers = array();
                        foreach ($pos_answers as $pos_answer)
                        {
                            $aAnswers[$pos_answer->PossibleID] = $pos_answer->Answer;
                        }
                        $cForminputs->addMultipleChoiceQuestion($question->QuestionID, $question->Question, $aAnswers);
                    }
                }
                else
                {
                    $cForminputs->addOpenQuestion($question->Question);
                }
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
