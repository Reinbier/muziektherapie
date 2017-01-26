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

    private function buildPage($content = "Geen invulling voor deze pagina..", $sidebar = true)
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
                        ' . $this->getLeftSideBar($sidebar) . '
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

    private function getLeftSideBar($value)
    {
        if ($value === "vragenlijst")
        {
            $buttons = '
                <p>
                    <a href="/nieuw/vragenlijst/" class="btn btn-success">Nieuwe vragenlijst</a>
                </p>
            ';
        }
        else
        {
            $buttons = '
                <p>
                    <a href="/nieuw/behandeling/" class="btn btn-warning">Nieuwe behandeling</a>
                </p>
                <p>
                    <a href="/nieuw/client/" class="btn btn-success">Nieuwe client</a>
                </p>
            ';
        }
        return '
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h3 class="panel-title">Snel naar:</h3>
                </div>
                <div class="panel-body">
                    ' . $buttons . '
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
            // set a limit for the amount of lines showed to the user
            $limit = 10;
            foreach ($registries as $log)
            {
                if ($limit == 0)
                { // when limit reaches 0, break this loop
                    break;
                }
                $message .= "
                    <li class='list-group-item'>
                        <span class='badge'>Aantal punten: " . $log["points"] . "</span>
                        <a href='/overzicht/gebruikers/" . $log["userID"] . "/'>" . $log["userName"] . "</a> heeft vragenlijst 
                        <a href='/overzicht/vragenlijsten/" . $log["QL_ID"] . "/'>" . $log["QL_Name"] . "</a> afgerond voor meting 
                        <a href='/overzicht/metingen/" . $log["MM_ID"] . "/'>" . $log["MM_Name"] . "</a> van 
                        <a href='/overzicht/behandelingen/" . $log["TMT_ID"] . "/'>" . $log["TMT_Name"] . "</a> op 
                        <span class='text-info'>" . NederlandseDatumTijd($log["date"]) . "</span>
                    </li>";
                // decrement limit
                $limit--;
            }
            $message .= '</ul>';
        }
        else
        {
            $message = '<p>Er zijn geen resultaten gevonden.</p>';
        }

        $content = '
            <div class="well">
            
                <h1>Meldingen</h1>
                ' . $message . '
            
            </div>
        ';

        return $this->buildPage($content);
    }

    public function getNewPage($subpage = null, $subsubpage = null, $subsubsubpage = null)
    {
        switch ($subpage)
        {
            case "behandeling":
                return $this->getNewTreatmentPage();
            case "meting":
                return $this->getNewMeasurementPage($subsubpage, $subsubsubpage);
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

    private function getNewTreatmentPage()
    {
        // set page vars
        $this->page = "nieuw";
        $this->title = "Nieuwe behandeling";
        $this->breadcrumbs = array("Home" => "home", "Nieuwe behandeling" => "");

        $cTreatment = new Treatment();
        $cUser = new User();
        $allClients = $cUser->getAllClients();

        $content = '
            <div class="well">
                <form class="form-horizontal" id="createTreatmentForm" onsubmit="return false">
                    <fieldset>
                        <legend>Nieuwe behandeling aanmaken</legend>
                        <div class="form-group">
                            <label for="treatmentName" class="col-lg-2 control-label">Naam</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" id="treatmentName" placeholder="Naam van behandeling" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-2 control-label">Selecteer client</label>
                            <div class="col-lg-10">
                                <select class="form-control" id="selectClient" name="selectClient" data-therapist="' . $this->userID . '" required>
                                    <option value="">Selecteer een client..</option>
                                    <optgroup label="Beschikbare clienten">
        ';
        foreach ($allClients as $client)
        {
            if (is_null($cTreatment->getActiveTreatmentByUserID($client->UserID)))
            {
                $content .= '<option value="' . $client->UserID . '">' . $client->Name . '</option>';
            }
        }
        $content .= '
                                    </optgroup>
                                </select>
                                <span class="help-block">Clienten die al in een actieve behandeling zitten zijn niet zichtbaar in deze lijst.</span>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-10 col-lg-offset-2 text-right">
                                <button type="submit" class="btn btn-primary" id="button-createTreatment" name="button-createTreatment">Aanmaken</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        ';

        return $this->buildPage($content);
    }

    private function getNewMeasurementPage($treatmentID, $qlID = null)
    {
        // set page vars
        $this->page = "nieuw";
        $this->title = "Nieuwe meting";
        $this->breadcrumbs = array("Home" => "home", "Nieuwe meting" => "");

        $cQuestionList = new QuestionList();
        $allQuestionLists = $cQuestionList->getAllQuestionLists();

        $content = '
            <div class="well">
                <form class="form-horizontal" id="createMeasurementForm" onsubmit="return false">
                    <fieldset>
                        <legend>Nieuwe meting toevoegen</legend>
                        <div class="form-group">
                            <label for="measurementName" class="col-lg-3 control-label">Naam</label>
                            <div class="col-lg-9">
                                <input type="text" class="form-control" id="measurementName" placeholder="Naam van meting" required>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="select" class="col-lg-3 control-label">Selecteer vragenlijst</label>
                            <div class="col-lg-9">
                                <select class="form-control" id="selectQlist" name="selectQlist" data-treatmentid="' . $treatmentID . '" required>
                                    <option value="">Selecteer een vragenlijst..</option>
                                    <optgroup label="Vragenlijsten">
        ';
        foreach ($allQuestionLists as $qlist)
        {
            $content .= '<option value="' . $qlist->QuestionlistID . '" ' . ($qlist->QuestionlistID == $qlID ? "selected" : "") . '>' . $qlist->Name . '</option>';
        }
        $content .= '
                                    </optgroup>
                                </select>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-lg-9 col-lg-offset-3 text-right">
                                <button type="submit" class="btn btn-primary" id="button-createMeasurement" name="button-createMeasurement">Aanmaken</button>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        ';

        return $this->buildPage($content);
    }

    private function getNewQuestionListPage()
    {
        // set page vars
        $this->page = "nieuw";
        $this->title = "Vragenlijst aanmaken";
        $this->breadcrumbs = array("Home" => "home", "Nieuwe vragenlijst" => "");

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
        $this->breadcrumbs = array("Home" => "home", "Nieuwe client" => "");

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
        $this->breadcrumbs = array("Home" => "home", "Nieuwe " . $roleName => "");

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
        $this->breadcrumbs = array("Home" => "home", "Nieuwe therapeut" => "");

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

    public function getOverviewPage($subpage = null, $subsubpage = null, $subsubsubpage = null, $subsubsubsubpage = null)
    {
        switch ($subpage)
        {
            case "behandelingen":
                return $this->getOverviewTreatmentsPage($subsubpage);
            case "metingen":
                return $this->getOverviewMeasurementsPage($subsubpage, $subsubsubpage, $subsubsubsubpage);
            case "vragenlijsten":
                return $this->getOverviewQuestionListsPage($subsubpage);
            case "gebruikers":
                return $this->getOverviewUsersPage($subsubpage);
            default:
                return $this->getHomePage();
        }
    }

    private function getOverviewTreatmentsPage($treatmentID = null)
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
            $this->breadcrumbs = array("Overzicht" => "overzicht", "Behandelingen" => "");

            $content = '
                <div class="well">
                    <table class="table table-striped table-hover dataTable">
                        <thead>
                            <tr>
                                <th>Naam</th>
                                <th>Client</th>
                                <th>Aantal metingen</th>
                                <th>Overige deelnemers</th>
                                <th>Status</th>
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
                    $clientID = $cTreatment->getClientIDbyTreatmentID($treatmentID);
                    $clientData = $cUser->getUserById($clientID);
                    $active = $cTreatment->isActive($treatmentID);
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
                                <td><a href='/overzicht/gebruikers/" . $clientID . "/' class='btn btn-link'>" . $clientData->Name . "</a></td>
                                <td>" . count($measurements) . "</td>
                                <td>" . $sKin . "</td>
                                <td>" . ($active ? "Lopend" : "Afgerond") . "</td>
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

        // the treatment doesnt exist, return to the overview (no parameters given)
        if (is_null($treatmentData))
        {
            return $this->getOverviewTreatmentsPage();
        }
        else
        {
            // set page vars
            $this->page = "overzicht";
            $this->title = $treatmentData->Name;
            $this->breadcrumbs = array("Overzicht" => "overzicht", "Behandelingen" => "behandelingen", $treatmentData->Name => "");

            $cQuestionlist = new QuestionList();
            $cMeasurement = new Measurement();
            $cUser = new User();

            $measurements = $cTreatment->getMeasurementsbyTreatmentID($treatmentID);
            $treatmentUsers = $cTreatment->getAllUsersInTreatment($treatmentID);

            $active = $cTreatment->isActive($treatmentID);

            if (!is_null($measurements))
            {
                $output = '<div class="list-group col-md-12">';

                foreach ($measurements as $measurement)
                {
                    // get average points
                    $avgPoints = $cMeasurement->getAveragePointsByMeasurementID($measurement->MeasurementID);

                    // check what to say about the completion of this measurement
                    $completeLabel = '<span class="label label-success">Volledig afgerond</span>';
                    $usersToComplete = '';
                    foreach ($treatmentUsers as $user)
                    {
                        $userData = $cUser->getUserById($user->UserID);
                        // determine if the therapist has competed this questionlist or not
                        if (!$cQuestionlist->isComplete($measurement->MeasurementID, $cQuestionlist->getQuestionListIDByMeasurementID($measurement->MeasurementID), $user->UserID))
                        {
                            $usersToComplete .= ($usersToComplete == '' ? $userData->Name : ', ' . $userData->Name);
                        }
                    }
                    if ($usersToComplete != '')
                    {
                        $completeLabel = '<span class="label label-warning">Nog in te vullen door: ' . $usersToComplete . '</span>';
                    }

                    $output .= '
                        <a href="/overzicht/metingen/' . $measurement->MeasurementID . '/' . $measurement->QuestionlistID . '/" class="list-group-item">
                            <h4 class="list-group-item-heading">' . $measurement->Name . '</h4>
                            <p class="list-group-item-text">
                                <span class="label label-primary">Gemiddeld: ' . $avgPoints . ' punten</span>
                                ' . $completeLabel . '
                            </p>
                        </a>
                    ';
                }
                $output .= '</div>';
            }
            else
            {
                $output = "<div class='col-md-12'><p>Geen metingen gevonden voor deze behandeling</p></div>";
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

            $finishedAlert = "";
            if (!$active)
            {
                $finishedAlert = '
                    <div class="alert alert-dismissible alert-info">
                        Deze behandeling werd gestart op <strong>' . NederlandseDatumTijd($treatmentData->Start) . '</strong> en afgerond op <strong>' . NederlandseDatumTijd($treatmentData->End) . '</strong>.
                    </div>
                ';
            }
            // show measurements within this treatment
            $content = $finishedAlert . '
                <div class="row">
                    <div class="col-md-' . ($active ? '6' : '4') . '">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">Metingen binnen deze behandeling</h3>
                            </div>
                            <div class="panel-body">
                                ' . $output;
            // add new measurement button
            if ($active)
            {
                $content .= '
                                <div class="col-md-12">
                                    <div class="btn-group">
                                        <a href="/nieuw/meting/' . $treatmentID . '/" class="btn btn-success">Nieuwe meting</a>
                                        <a href="#" class="btn btn-success dropdown-toggle tooltip-toggle" data-toggle="dropdown" aria-expanded="false" data-placement="right" title="" data-original-title="Selecteer een vragenlijst"><span class="caret"></span></a>
                                        <ul class="dropdown-menu">
            ';
                $allQuestionLists = $cQuestionlist->getAllQuestionLists();
                foreach ($allQuestionLists as $qlist)
                {
                    $content .= '<li><a href="/nieuw/meting/' . $treatmentID . '/' . $qlist->QuestionlistID . '">' . $qlist->Name . '</a></li>';
                }
                $content .= '
                                        </ul>
                                    </div>
                                </div>
            ';
            }
            $content .= '
                            </div>
                        </div>
                    </div>
            ';
            if ($active)
            {
                $content .= '
                    <div class="col-md-6">
                        <div class="panel panel-primary">
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

                    <div class="col-md-6">
                        <div class="panel panel-primary">
                            <div class="panel-heading">
                                <h3 class="panel-title">Acties</h3>
                            </div>
                            <div class="panel-body">
                                <p class="text-muted">Deze behandeling is aangemaakt op ' . NederlandseDatumTijd($treatmentData->Start) . '</p>
                                <a href="javascript:void(0);" class="btn btn-success actionTreatment" data-action="finish" data-treatmentid="' . $treatmentID . '">Behandeling afronden</a>
                                <a href="javascript:void(0);" class="btn btn-danger actionTreatment" data-action="delete" data-treatmentid="' . $treatmentID . '">Verwijder deze behandeling</a>
                            </div>
                        </div>
                    </div>
                ';
            }
            $content .= '
                    <div class="col-md-' . ($active ? '12' : '8') . '">
                        <div class="panel panel-info">
                            <div class="panel-heading">
                                <h3 class="panel-title">Globale voortgang</h3>
                            </div>
                            <div class="panel-body">
                                <div id="progressChart" data-treatmentid="' . $treatmentID . '" data-role="therapist" style="height: 40rem;">

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ';

            return $this->buildPage($content, false);
        }
    }

    private function getOverviewMeasurementsPage($measurementID = null, $questionListID = null, $userID = null)
    {
        $this->page = "overzicht";
        $this->title = "Metingen overzicht";
        $this->breadcrumbs = array("Overzicht" => "overzicht", "Metingen" => "");

        // no measurementid given, we do not know the treatment so go to the overview
        if (is_null($measurementID))
        {
            return $this->getOverviewTreatmentsPage();
        }
        else if (is_null($questionListID)) // no questionlist given but we have a measurement id
        {
            return $this->getOverviewMeasurementsDetailsPage($measurementID, $questionListID);
        }
        else // both measurementid AND questionlistid are NOT NULL, show the questionlist to fill in for this user
        {
            return $this->getOverviewMeasurementsQuestionListDetailsPage($measurementID, $questionListID, $userID);
        }
        // default: send the user back to treatments
        return $this->getOverviewTreatmentsPage();
    }

    private function getOverviewMeasurementsDetailsPage($measurementID, $questionListID)
    {
        $cTreatment = new Treatment();
        $cQuestionList = new QuestionList();
        $cMeasurement = new Measurement();
        $cUser = new User();

        $measurement = $cMeasurement->getMeasurement($measurementID);
        $measurementName = $measurement->Name;

        $treatmentID = $measurement->TreatmentID;
        $treatment = $cTreatment->getTreatmentByTreatmentID($treatmentID);
        $treatmentName = $treatment->Name;

        $this->page = "overzicht";
        $this->title = $treatmentName . " - " . $measurementName;
        $this->breadcrumbs = array("Overzicht" => "overzicht", "Behandelingen" => "behandelingen", $treatmentName => $treatmentID, $measurementName => "");
        $treatmentCheck = $cQuestionList->checkTreatment($this->userID, $questionListID, $measurementID);

        // check if the treatment exists for this user (therapist)
        if (is_null($treatmentCheck))
        {
            return $this->getOverviewTreatmentsPage(); // return to the overview
        }
        else // it exists!
        {
            $output = "";

            // set vars
            $allUsers = $cTreatment->getAllUsersInTreatment($treatmentID);
            $questionlistID = $measurement->QuestionlistID;

            foreach ($allUsers as $user)
            {
                $userID = $user->UserID;
                $userData = $cUser->getUserById($userID);
                // check if the user has completed his/her questionlist
                if ($cQuestionList->isComplete($measurementID, $questionlistID, $userID))
                {
                    $labelComplete = '<div class="label label-success">Volledig ingevuld</div>';
                }
                else
                {
                    $labelComplete = '<div class="label label-warning">Nog niet volledig ingevuld</div>';
                }

                $points = $cMeasurement->getPointsByUserID($measurementID, $userID);

                $output .= '
                    <div class="col-md-4">
                        <div class="well">
                            ' . $labelComplete . '
                            <h2> ' . $userData->Name . '</h2>
                            <p class="lead">Punten: ' . ($cQuestionList->isComplete($measurementID, $questionlistID, $userID) ? $points : "n.t.b.") . '</p>
                            <a href="/overzicht/metingen/' . $measurementID . '/' . $questionlistID . '/' . $userID . '/" class="btn btn-primary">Naar vragenlijst</a>
                         </div>
                    </div>
                ';
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

    private function getOverviewMeasurementsQuestionListDetailsPage($measurementID, $questionListID, $userID)
    {
        $cQuestionList = new QuestionList();
        $questionListName = $cQuestionList->getQuestionListNameByID($questionListID);
        $cMeasurement = new Measurement();
        $measurement = $cMeasurement->getMeasurement($measurementID);
        $cTreatment = new Treatment();
        $treatment = $cTreatment->getTreatmentByTreatmentID($measurement->TreatmentID);
        $treatmentName = $treatment->Name;

        $this->page = "overzicht";
        $this->title = "Vragenlijst invullen - " . $questionListName;
        $this->breadcrumbs = array("Overzicht" => "overzicht", "Metingen" => "metingen", $treatmentName . " - " . $measurement->Name => $measurementID . "/" . $questionListID, $questionListName => "");
        $treatmentCheck = $cQuestionList->checkTreatment($userID, $questionListID, $measurementID);

        if (is_null($treatmentCheck))
        {
            return $this->getOverviewMeasurementsDetailsPage($measurementID, $questionListID); // return to the overview of this measurement
        }
        else
        {
            $formbody = "Geen vragen gevonden";
            $cQuestion = new Question();
            $cForminputs = new FormInputs();
            $cForminputs->setLabelWidth(1);
            $cForminputs->setInputWidth(11);
            $cForminputs->disableMandatoryNotification();

            $questions = $cQuestionList->getQuestions($questionListID);
            $disabled = "";
            $submitButton = true;

            // first check if the therapist id is the same as that of this questionList
            $labelComplete = "";
            if ($this->userID !== $userID)
            {
                // disabled the questionlist
                $disabled = "disabled";
                $submitButton = false;
                // set the label
                $labelComplete = '<p><span class="label label-warning">Deze vragenlijst is van een andere gebruiker</span></p>';
            }
            if (!is_null($questions))
            {
                if ($cQuestionList->isComplete($measurementID, $questionListID, $userID))
                {
                    $disabled = "disabled";
                    // set the label
                    $labelComplete = '<p><span class="label label-success">Deze vragenlijst is afgerond</span></p>';
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
                if (!($cQuestionList->isComplete($measurementID, $questionListID, $userID)) && $submitButton)
                {
                    $cForminputs->addButton("fillInQuestionList", "Opslaan");
                }

                $formbody = $cForminputs->createFormBody();
            }

            $cUser = new User();
            $userData = $cUser->getUserById($userID);
            $userName = $userData->Name;

            $content = '<div class="row">
                <div class="col-md-12 lead">Gebruiker: ' . $userName . '</div>
                <div class="col-md-12">
                    <div class="well">
                        ' . $labelComplete . '
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
            $this->breadcrumbs = array("Overzicht" => "overzicht", "Gebruikers" => "");

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
        $this->breadcrumbs = array("Overzicht" => "overzicht", "Gebruikers" => "gebruikers", $userDetails->Name => "");

        $cTreatment = new Treatment();
        $cMeasurement = new Measurement();

        $output = "";
        $treatment = $cTreatment->getActiveTreatmentByUserID($userID);

        if (!is_null($treatment))
        {
            $treatmentID = $treatment->TreatmentID;
            $measurements = $cTreatment->getMeasurementsbyTreatmentID($treatmentID);

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
                                <p class='points'>" . (!$cQuestionlist->isComplete($measurement->MeasurementID, $questionListID, $userID) ? "n.t.b." : $points) . "</p>
                                " . $measurement->Name . "
                            </div>
                        </div> ";
                }
            }
        }
        else
        {
            $output = "<div class='col-md-12'>Geen metingen gevonden voor deze behandeling</div>";
            $treatmentID = "0";
        }

        $content = '
        <div class="row">
            <div class="col-md-4">
                <div class="row">
                    ' . $output . '
                </div>
            </div>
            <div class="col-md-8">
                <div id="progressChart" data-treatmentid="' . $treatmentID . '" data-role="client" style="height: 40rem;">

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
            $this->breadcrumbs = array("Overzicht" => "overzicht", "Vragenlijsten" => "");

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

            return $this->buildPage($content, "vragenlijst");
        }
    }

    public function getOverviewQuestionListsDetailsPage($questionListID)
    {
        $cQuestionlist = new QuestionList();
        $questionListName = $cQuestionlist->getQuestionListNameByID($questionListID);
        $this->page = "overzicht";
        $this->title = $questionListName;
        $this->breadcrumbs = array("Overzicht" => "overzicht", "Vragenlijsten" => "vragenlijsten", $questionListName => "");

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
                        $cForminputs->addMultipleChoiceQuestion($question->QuestionID, $question->Question, $aAnswers, null, null, "disabled");
                    }
                }
                else
                {
                    $cForminputs->addOpenQuestion($question->QuestionID, $question->Question, null, null, "disabled");
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
