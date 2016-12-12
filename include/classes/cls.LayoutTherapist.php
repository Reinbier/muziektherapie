<?php

/**
 * @author: Reinier Gombert
 * @date: 5-dec-2016
 */
class LayoutTherapist extends Layout
{

    private $page;
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
                        <a class="navbar-brand" href="#"></a>
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
                return $this->getTherapistAanmakenPage();
            case "overzicht":
            default:
                return $this->getTherapistOverzichtPage();
        }
    }

    private function getTherapistCreatePage()
    {
        $content = '
            <form class="form-horizontal">
                <fieldset>
                            <h5>Algemeen</h5>
                            <div class="form-group">
                                <label for="inputNaam" class="col-lg-2 control-label">Naam</label>
                                <div class="col-lg-10">
                                    <input type="text" class="form-control" id="inputNaam" placeholder="Naam">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputANaam" class="col-lg-2 control-label">Adres</label>
                                <div class="col-lg-10">
                                    <input type="text" class="form-control" id="inputAdres" placeholder="Adres">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputWoonplaats" class="col-lg-2 control-label">Woonplaats</label>
                                <div class="col-lg-10">
                                    <input type="text" class="form-control" id="inputWoonplaats" placeholder="Woonplaats">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputTelVast" class="col-lg-2 control-label">Telefoonnummer vast </label>
                                <div class="col-lg-10">
                                    <input type="text" class="form-control" id="inputTelVast" placeholder="Telefoon vast">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputTelMob" class="col-lg-2 control-label">Telefoon mobiel</label>
                                <div class="col-lg-10">
                                    <input type="text" class="form-control" id="inputTelMob" placeholder="Telefoon mobiel">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputEmail" class="col-lg-2 control-label">E-Mail</label>
                                <div class="col-lg-10">
                                    <input type="text" class="form-control" id="inputEmail" placeholder="E-mail">
                                </div>
                            </div>
                            <h5>Persoonlijk</h5>
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Gehuwd?</label>
                                <div class="col-lg-10">
                                    <div class="radio-inline">
                                        <label>
                                            <input type="radio" name="marriage" value="ja">Ja
                                        </label>
                                    </div>
                                    <div class="radio-inline">
                                        <label>
                                            <input type="radio" name="marriage" value="nee">Nee
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-lg-2 control-label">Samenwonend?</label>
                                <div class="col-lg-10"><div class="radio-inline">
                                    <label>
                                        <input type="radio" name="living2gether" value="ja">Ja
                                    </label>
                                </div>
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" name="living2gether" value="nee">Nee
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword" class="col-lg-2 control-label">Geboortedatum</label>
                            <div class="col-lg-10">
                                <input type="date" class="form-control" id="inputBDay" />
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputPassword" class="col-lg-2 control-label">Geslacht</label>
                            <div class="col-lg-10">
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" name="gender" id="inputMan" value="male" checked> Man
                                    </label>
                                </div>
                                <div class="radio-inline">
                                    <label>
                                        <input type="radio" name="gender" id="inputVrouw" value="female"> Vrouw
                                    </label>
                                </div>
                            </div>
                        </div>

                        <h5>Opleiding</h5>

                        <div class="form-group">
                            <label for="inputStudy" class="col-lg-2 control-label">Hoogste niveau van opleiding</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" id="inputStudy" placeholder="niveau opleiding">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputStudyType" class="col-lg-2 control-label">Type opleiding</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" id="inputStudyType" placeholder="type opleiding">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputWork" class="col-lg-2 control-label">Werkzaamheden</label>
                            <div class="col-lg-10">
                                <textarea class="form-control" id="inputWork"> </textarea>
                            </div>
                        </div>

                        <h5>Hulpverleners</h5>

                        <div class="form-group">
                            <label for="inputSocialworker" class="col-lg-2 control-label">Verwijzer</label>
                            <div class="col-lg-10">
                                <input type="text"class="form-control" id="inputSocialworker">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputDoctor" class="col-lg-2 control-label">Huisarts</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" id="inputDoctor">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputClinic" class="col-lg-2 control-label">Huisartsenpraktijk</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" id="inputClinic">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="inputWorkers" class="col-lg-2 control-label">Betrokken behandelaars</label>
                            <div class="col-lg-10">
                                <input type="text" class="form-control" id="inputWorkers">
                            </div>
                        </div>

                        <h5>Diagnostiek</h5>

                        <div class="form-group">
                            <label for="inputProblems" class="col-lg-2 control-label">Beschreven problematiek</label>
                            <div class="col-lg-10">
                                <textarea class="form-control" id="inputProblems"></textarea>
                            </div>
                        </div>
                        <div class="form-group">
                            <label class="col-lg-2 control-label">Officiele diagnose?</label>
                            <div class="col-lg-10"><div class="radio-inline">
                                <label>
                                    <input type="radio" name="diagnose" value="true">Ja
                                </label>
                            </div>
                            <div class="radio-inline">
                                <label>
                                    <input type="radio" name="diagnose" value="false">Nee
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputDetermined" class="col-lg-2 control-label">Welke professional heeft de diagnose gesteld?</label>
                        <div class="col-lg-10">
                            <input type="text"  class="form-control" id="inputDetermined">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputDeterminedBy" class="col-lg-2 control-label">Hoe is de diagnose gesteld?</label>
                        <div class="col-lg-10">
                            <input type="text"  class="form-control" id="inputDeterminedBy">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputCause" class="col-lg-2 control-label">Aanleiding depressieve klachten</label>
                        <div class="col-lg-10">
                            <textarea type="text"  class="form-control" id="inputDetermined"></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="inputDuration" class="col-lg-2 control-label">Duur van depressieve klachten</label>
                        <div class="col-lg-10">
                            <input type="text"  class="form-control" id="inputDuration">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-lg-2 control-label">Ernst van depressieve klachten</label>
                        <div class="col-lg-10"><div class="radio-inline">
                            <label>
                                <input type="radio" name="severity" value="licht">Licht
                            </label>
                        </div>
                        <div class="radio-inline">
                            <label>
                                <input type="radio" name="severity" value="matig">Matig
                            </label>
                        </div>
                        <div class="radio-inline">
                            <label>
                                <input type="radio" name="severity" value="ernstig">Ernstig
                            </label>
                        </div>
                        <div class="radio-inline">
                            <label>
                                <input type="radio" name="severity" value="zeer ernstig">Zeer ernstig
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputFallback" class="col-lg-2 control-label">Aantal keren terugval</label>
                    <div class="col-lg-10">
                        <input type="text"  class="form-control" id="inputFallback">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputWinterdepression" class="col-lg-2 control-label">Lichtgevoeligheid</label>
                    <div class="col-lg-10">
                        <input type="text"  class="form-control" id="inputWinterdepression">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputOtherComplaints" class="col-lg-2 control-label">Andere klachten</label>
                    <div class="col-lg-10">
                        <textarea type="text"  class="form-control" id="inputOtherComplaints"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputOtherComplaints" class="col-lg-2 control-label">Sociaal netwerk</label>
                    <div class="col-lg-10">
                        <textarea type="text"  class="form-control" id="inputOtherComplaints"></textarea>
                    </div>
                </div>

                <h5>Behandeling</h5>

                <div class="form-group">
                    <label for="inputGoal" class="col-lg-2 control-label">Doel muziektherapie</label>
                    <div class="col-lg-10">
                        <input type="text"  class="form-control" id="inputGoal">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputAntidepressionMeds" class="col-lg-2 control-label">Antidepressiva</label>
                    <div class="col-lg-10">
                        <textarea class="form-control" id="inputAntidepressionMeds"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputOtherTreatments" class="col-lg-2 control-label">Andere behandelingen t.b.v depressieve klachten</label>
                    <div class="col-lg-10">
                        <textarea class="form-control" id="inputOtherTreatments"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputOtherMeds" class="col-lg-2 control-label">Andere medicatie die invloed heeft op depressieve klachten</label>
                    <div class="col-lg-10">
                        <textarea class="form-control" id="inputOtherMeds"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputBefore" class="col-lg-2 control-label">Eerder muziektherapeutische behandelingen?</label>
                    <div class="col-lg-10">
                        <div class="radio-inline">
                            <label>
                                <input type="radio" name="before" value="true">Ja
                            </label>
                        </div>
                        <div class="radio-inline">
                            <label>
                                <input type="radio" name="before" value="false">Nee
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputMusicExperience" class="col-lg-2 control-label">Muziekervaring</label>
                    <div class="col-lg-10">
                        <textarea class="form-control" id="inputMusicExperience"></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputMusicExperience" class="col-lg-2 control-label">Muzikale voorkeuren</label>
                    <div class="col-lg-10">
                        <textarea class="form-control" id="inputMusicExperience"></textarea>
                    </div>
                </div>

                <h5>Systematische n=1 methode</h5>

                <div class="form-group">
                    <label for="inputOtherMeds" class="col-lg-2 control-label">Akkoord dat bekenden vragenlijst invullen?</label>
                    <div class="col-lg-10">
                        <div class="radio-inline">
                            <label>
                                <input type="radio" name="agree" value="true">Ja
                            </label>
                        </div>
                        <div class="radio-inline">
                            <label>
                                <input type="radio" name="agree" value="false">Nee
                            </label>
                        </div>
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputChosenNetwork" class="col-lg-2 control-label">Gekozen netwerk die vragenlijst gaat invullen</label>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="inputChosenNetwork">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputEmailChosenNetwork" class="col-lg-2 control-label">Email adressen van dit netwerk</label>
                    <div class="col-lg-10">
                        <input type="text" class="form-control" id="inputEmailChosenNetwork">
                    </div>
                </div><div class="form-group">
                <div class="col-lg-3 col-lg-offset-9">
                    <button type="reset" class="btn btn-default">Cancel</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </div>
        </fieldset>
    </form>
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
