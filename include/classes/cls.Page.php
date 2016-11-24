<?php

/**
 * @author: Reinier Gombert
 * @date: 16-nov-2016
 */

class Page extends DAL
{

    private $page;
    private $subpage;

    public function __construct($page = "home", $subpage = null)
    {
        parent::__construct();
        $this->page = $page;
        $this->subpage = $subpage;
    }
    
    public function displayHeader()
    {
        $return = '<img class="img-responsive" src="images/Weblogo.png" alt="Sonja Aalbers" />';
        
        if(isset($_SESSION["userID"]))
        {
            $cUser = new User($_SESSION["userID"]);
            if($cUser->isTherapist())
            {
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
                                    <li class="active"><a href="therapeutHome.html">Admin home <span class="sr-only">(current)</span></a></li>
                                    <li class="dropdown">
                                        <a href="#" class="dropdown-toggle" data-toggle="dropdown" role="button" aria-expanded="false">Client <span class="caret"></span></a>
                                        <ul class="dropdown-menu" role="menu">
                                            <li><a href="grafiek.html">Weergeven</a></li>
                                            <li><a href="AanmakenCliënt.html">Aanmaken</a></li>
                                            <li><a href="#">Bewerken</a></li>
                                            <li><a href="#">Verwijderen</a></li>
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
                                    <li><a href="index.html">Uitloggen <span class="sr-only">(current)</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                ';
            }
            else
            {
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
                                    <li><a href="therapeutHome.html">Home <span class="sr-only">(current)</span></a></li>
                                </ul>
                                <ul class="nav navbar-nav navbar-right">
                                    <li class="active"><a href="index.html">Login <span class="sr-only">(current)</span></a></li>
                                </ul>
                            </div>
                        </div>
                    </nav>
                ';
            }
            
            
        }
        else
        {
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
                                <li><a href="therapeutHome.html">Home <span class="sr-only">(current)</span></a></li>
                            </ul>
                            <ul class="nav navbar-nav navbar-right">
                                <li class="active"><a href="index.html">Login <span class="sr-only">(current)</span></a></li>
                            </ul>
                        </div>
                    </div>
                </nav>
            ';
        }
        echo $return;
    }
    
    public function displayContent()
    {
        switch ($this->page)
        {
            case "home":
                $return = $this->getHomeContent();
                break;
            case "login":
            default:
                $return = $this->getLoginContent();
                break;
        }
        
        echo $return;
    }
    
    public function displayFooter()
    {
        $return = '
            <footer class="bd-footer text-muted">
                <div class="container">
                    <ul class="bd-footer-links">
                        <li><a href="https://facebook.com">Facebook</a></li>
                        <li><a href="https://twitter.com/">Twitter</a></li>
                        <li><a href="https://plus.google.com/">Google+</a></li>
                    </ul>
                    <p>Project Muziektherapie</p>
                    <p>Copyright 2016. All rights reserved.</p>
                </div>
            </footer>
        ';
        echo $return;
    }
    
    private function getLoginContent()
    {
        $return = '
            <div class="row">

                <div class="col-md-6 col-md-offset-3 well">

                    <form class="form-horizontal">
                        <fieldset>
                            <legend>Inloggen</legend>
                            <div class="form-group">
                                <label for="inputEmail" class="col-lg-2 control-label">Email</label>
                                <div class="col-lg-10">
                                    <input type="text" class="form-control" id="inputEmail" placeholder="Email">
                                </div>
                            </div>
                            <div class="form-group">
                                <label for="inputPassword" class="col-lg-2 control-label">Password</label>
                                <div class="col-lg-10">
                                    <input type="password" class="form-control" id="inputPassword" placeholder="Password">
                                    <div class="checkbox">

                                    </div>
                                    <div class="form-group">
                                        <div class="col-lg-10 col-lg-offset-6">
                                            <button type="reset" class="btn btn-default">ww vergeten?</button>
                                            <button type="submit" class="btn btn-primary">Submit</button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </fieldset>
                    </form>

                </div>

            </div>
        ';
        
        return $return;
    }
    
    private function getHomeContent()
    {
        
    }
}