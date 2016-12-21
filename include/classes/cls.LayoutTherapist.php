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
                            <li ' . ($this->page == "home" ? 'class="active"' : '') . '><a href="/home/">Home</a></li>
                            <li class="dropdown ' . ($this->page == "client" ? 'active' : '') . '">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Client <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="/client/aanmaken/">Aanmaken</a></li>
                                    <li><a href="/client/overzicht/">Overzicht</a></li>
                                </ul>
                            </li>
                            <li class="dropdown ' . ($this->page == "therapeut" ? 'active' : '') . '">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Therapeut <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="/therapeut/aanmaken/">Aanmaken</a></li>
                                    <li><a href="/therapeut/overzicht/">Overzicht</a></li>
                                </ul>
                            </li>
                            <li class="dropdown ' . ($this->page == "vragenlijst" ? 'active' : '') . '">
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
        if($sidebar)
        {
            // build the page with the sidebar
            $return = ' 
                <div class="row">
                    <div class="col-md-12 lead"><h2>' . $this->title . '</h2></div>
                </div>
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
        $this->title = "Therapeut aanmaken";
        $cForm = new FormInputs();
        $cForm->addTextInput("Naam");
        $cForm->addTextInput("Adres");
        $cForm->addTextInput("Woonplaats");
        $cForm->addTextInput("Telefoon");
        $cForm->addTextInput("Email");
        $cForm->addTextInput("Wachtwoord", "password");
        $cForm->addRadioGroup("Geslacht", array("Man", "Vrouw"));
        $cForm->addButton("createTherapist", "Aanmaken");
        $cForm->addResetButton();
        $formBody = $cForm->createFormBody();
        
        $content = '
            <div class="well">
                <form class="form-horizontal" id="createTherapistForm">
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
        switch($subpage)
        {
            case "aanmaken":
                return $this->getClientAanmakenPage();
            case "overzicht":
            default:
                return $this->getClientOverzichtPage();
        }
    }

    private function getClientCreatePage()
    {
        
    }
    
    private function getClientOverviewPage()
    {
        
    }
    
    public function getQuestionListPage($subpage = null)
    {
        switch($subpage)
        {
            case "aanmaken":
                return $this->getQuestionListAanmakenPage();
            case "overzicht":
            default:
                return $this->getQuestionListOverzichtPage();
        }
    }

    private function getQuestionListCreatePage()
    {
        
    }
    
    private function getQuestionListOverviewPage()
    {
        
    }

}
