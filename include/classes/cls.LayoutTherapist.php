<?php

/**
 * @author: Reinier Gombert
 * @date: 5-dec-2016
 */
class LayoutTherapist extends Layout
{

    public function __construct()
    {
        parent::__construct();
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
                            <li class="active"><a href="/home/">Home <span class="sr-only">(current)</span></a></li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Client <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="/client/list/">Weergeven</a></li>
                                    <li><a href="/client/create/">Aanmaken</a></li>
                                    <li><a href="/client/edit/">Bewerken</a></li>
                                    <li><a href="/client/delete/">Verwijderen</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Therapeut <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="#">Aanmaken</a></li>
                                    <li><a href="#">Bewerken</a></li>
                                    <li><a href="#">Verwijderen</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Naaste <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="#">Aanmaken</a></li>
                                    <li><a href="#">Bewerken</a></li>
                                    <li><a href="#">Verwijderen</a></li>
                                </ul>
                            </li>
                            <li class="dropdown">
                                <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Vragenlijst <span class="caret"></span></a>
                                <ul class="dropdown-menu" role="menu">
                                    <li><a href="AanmakenVragenlijst.html">Aanmaken</a></li>
                                    <li><a href="#">Bewerken</a></li>
                                    <li><a href="#">Verwijderen</a></li>
                                </ul>
                            </li>
                        </ul>
                        <ul class="nav navbar-nav navbar-right">
                            <li class="navbar-text">Ingelogd als Sonja</li>
                            <li><a href="?logout">Uitloggen <span class="sr-only">(current)</span></a></li>
                        </ul>
                    </div>
                </div>
            </nav>
        ';
        return $return;
    }

    public function getHomePage()
    {
        $return = '
                <div class="container-fluid">
                
                    <div class="row">

                        <div class="col-md-3">
                            <div class="panel panel-default">
                                <div class="panel-heading">
                                  <h3 class="panel-title">Meteen naar:</h3>
                                </div>
                                <div class="panel-body">
                                  <a href="metingstarten.html" class="btn btn-success">Nieuwe meting</a>
                                </div>
                              </div>
                        </div>

                        <div class="col-md-6 col-md-offset-1 well">
                            <h3>Meldingen</h3>
                            <p>Op dit moment geen meldingen om weer te geven</p>
                        </div>

                    </div>
                        
                </div>
            ';

        return $this->getHeader() . parent::getContent($return) . $this->getFooter();
    }

}
