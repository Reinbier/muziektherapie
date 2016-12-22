<?php

/**
 * @author: Reinier Gombert
 * @date: 5-dec-2016
 */
class LayoutClient extends Layout
{

    private $page;
    private $title;
    private $userID;
    private $cUser;
    private $cTreatment;
    private $cMeasurement;

    public function __construct($userID)
    {
        parent::__construct();
        $this->userID = $userID;
        $this->cUser = new User();
        $this->cTreatment = new Treatment();
        $this->cMeasurement = new Measurement();
    }

    private function buildPage($content = "No content found..", $sidebar = true)
    {
        if($sidebar)
        {
            // build the page with the sidebar
            $return = '
            <div class="row">
                <div class="col-md-12 lead">
                    <h2>' . $this->title . '</h2>
                </div>
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
                <div class="col-md-12 lead">
                    <h2>' . $this->title . '</h2>
                </div>
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

    public function getHeader()
    {
        $header = parent::getHeader();

        $header .= '
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
                        <li ' . ($this->page == "voortgang" ? 'class="active"' : '') . '><a href="/voortgang/">Voortgang</a></li>
                        <li ' . ($this->page == "vragenlijst" ? 'class="active"' : '') . '><a href="/vragenlijst/">Vragenlijst</a></li>
                    </ul>
                    <ul class="nav navbar-nav navbar-right">
                        <li class="navbar-text">Ingelogd als '. $this->cUser->getUserById($this->userID)->Name .'</li>
                        <li><a href="/?logout">Uitloggen <span class="sr-only">(current)</span></a></li>
                    </ul>
                </div>
            </div>
        </nav>
        ';
        return $header;
    }

    public function getHomePage()
    {
        $this->page = "home";
        $this->title = "Startpagina";
        $announcement ="";

        $content = '
        <div class="row">
            <h1>Welkom '. $this->cUser->getUserById($this->userID)->Name .'</h1>
            <div class="col-md-6 col-md-offset-1 well">
                <h3>Meldingen</h3>
                <p class="lead">Op dit moment geen meldingen om weer te geven</p>
            </div>
        </div>
        ';

        return $this->buildPage($content, false);
    }

    public function getProgressPage()
    {
        $output = "";
        $treatment = $this->cTreatment->getTreatmentByUserID($this->userID);
        $NumOfMeasurements = $this->cMeasurement->getTotalMeasurementsByTreatmentID($treatment->TreatmentID);
        $measurements = $this->cTreatment->getMeasurementsbyTreatmentID($treatment->TreatmentID);
        
        if ($measurements) 
        {
            foreach ($measurements as $measurement)
            {
                $points = $this->cMeasurement->getPointsByUserID($measurement->MeasurementID, $this->userID);
                $output .= "
                <div class='col-md-6'>
                    <div class='well text-center'>
                        <p class='points'>" . ($points != NULL ? $points : "n.t.b.") . "</p>
                        " . $measurement->Name . "
                    </div>
                </div> ";
            }
        }

        if($output == "")
        {
            $output = "<div class='col-md-12'>Geen metingen gevonden voor deze behandeling</div>";
        }

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
                        ' . $output  .'
                        </div>
                    </div>
                </div>


            </div>
            <div class="col-md-8">
                <div id="progressChart" style="height: 40rem;">

                </div>
            </div>
        </div>';

return $this->buildPage($content, false);
}

}
