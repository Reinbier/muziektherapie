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
                        <button type="button" class="navbar-toggle collapsed" data-toggle="collapse" data-target="#bs-example-navbar-collapse-1">
                            <span class="sr-only">Toggle navigation</span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                            <span class="icon-bar"></span>
                        </button>
                    </div>

                    <div class="collapse navbar-collapse" id="bs-example-navbar-collapse-1">
                        <ul class="nav navbar-nav">
                            <li' . ($this->page == "home" ? ' class="active"' : '') . '><a href="/home/">Home</a></li>
                            <li class="dropdown' . ($this->page == "client" ? ' active' : '') . '">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Client <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="/client/aanmaken/">Aanmaken</a></li>
                                    <li><a href="/client/overzicht/">Overzicht</a></li>
                                </ul>
                            </li>
                            <li class="dropdown' . ($this->page == "therapeut" ? ' active' : '') . '">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Therapeut <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="/therapeut/aanmaken/">Aanmaken</a></li>
                                </ul>
                            </li>
                            <li class="dropdown' . ($this->page == "vragenlijst" ? ' active' : '') . '">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Vragenlijst <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="/vragenlijst/aanmaken/">Aanmaken</a></li>
                                    <li><a href="/vragenlijst/overzicht/">Overzicht</a></li>
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
        
        if($sidebar)
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
        switch($subpage)
        {
            case "aanmaken":
                return $this->getTherapistCreatePage();
            case "overzicht":
            default:
                return $this->getTherapistOverviewPage();
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
        
    }
    
    public function getClientPage($subpage = null)
    {
        if(is_numeric($subpage))
        {
            return $this->getClientDetailsPage($subpage);
        }
        else
        {
            switch($subpage)
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
        if(!is_null($allClients))
        {
            foreach($allClients as $client) // print info in table
            {
                $content .= "
                        <tr>
                            <td><a href='/client/" . $client->UserID . "/' class='btn btn-link'>" . $client->Name . "</a></td>
                            <td>" . $client->Email . "</td>
                            <td>" . $client->Gender . "</td>
                            <td>" . $client->Place . "</td>
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
        
        
        return $this->buildPage($userDetails->Place, false);
    }


    public function getQuestionListPage($subpage = null)
    {
        switch($subpage)
        {
            case "aanmaken":
                return $this->getQuestionListCreatePage();
            case "overzicht":
            default:
                return $this->getQuestionListOverviewPage();
        }
    }

    private function getQuestionListCreatePage()
    {
        
    }
    
    private function getQuestionListOverviewPage()
    {
        
    }

}
