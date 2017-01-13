<?php

/**
 * @author: Reinier Gombert, Ronald van der Weide
 * @date: 5-dec-2016
 */
class LayoutNaaste extends Layout
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
                            <li ' . ($this->page == "vragenlijst" ? 'class="active"' : '') . '"><a href="/vragenlijst/">Vragenlijst</a></li>
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
            
                <h3>Welkom</h3>
                <p></p>
            
            </div>
        ';

        return $this->buildPage($content);
    }
    
    public function getQuestionListPage($subpage = null)
    {
        $this->page = "vragenlijst";
        $this->title = "Vragenlijst";
        
        $content = '
            
        ';
        
        return $this->buildPage($content);
    }
}
