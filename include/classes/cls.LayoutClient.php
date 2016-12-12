<?php

/**
 * @author: Reinier Gombert
 * @date: 5-dec-2016
 */
class LayoutClient extends Layout
{

    private $page;
    private $userID;
    private $cUser;

    public function __construct($userID)
    {
        parent::__construct();
        $this->userID = $userID;
        $this->cUser = new User();
    }

    public function getHeader()
    {
        $return = parent::getHeader();

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
        return $return;
    }

    public function getHomePage()
    {
        $this->page = "home";

        $return = '
        <div class="container-fluid">

            <div class="row">

                <h1>Welkom '. $this->cUser->getUserById($this->userID)->Name .'<h1>
                    <div class="col-md-6 col-md-offset-1 well">
                        <h3>Meldingen</h3>
                        <p>Op dit moment geen meldingen om weer te geven</p>
                    </div>
                </div>
            </div>
            ';

            return $this->getHeader() . parent::getContent($return) . $this->getFooter();
        }

        public function getProgressPage()
        {
            $this->page = "voortgang";
            $return = '<div class="container-fluid">
            <div class="row">

            </div>
        </div>';

        return $this->getHeader() . parent::getContent($return) . $this->getFooter();
    }

}
