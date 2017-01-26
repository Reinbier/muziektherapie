<?php

/**
 * @author: Reinier Gombert
 * @date: 16-nov-2016
 */

class Page extends DAL
{

    private $page;
    private $subpage;
    private $subsubpage;
    private $subsubsubpage;
    private $subsubsubsubpage;

    public function __construct($page = "home", $subpage = null, $subsubpage = null, $subsubsubpage = null, $subsubsubsubpage = null)
    {
        parent::__construct();
        $this->page = $page;
        $this->subpage = $subpage;
        $this->subsubpage = $subsubpage;
        $this->subsubsubpage = $subsubsubpage;
        $this->subsubsubsubpage = $subsubsubsubpage;
    }
    
    public function display()
    {
        if(isset($_SESSION["userID"]))
        {
            $cUser = new User($_SESSION["userID"]);
            
            if($cUser->isTherapist())
            {
                echo $this->buildLayoutTherapist($_SESSION["userID"]);
            }
            else if($cUser->isClient())
            {
                echo $this->buildLayoutClient($_SESSION["userID"]);
            }
            else
            {
                echo $this->buildLayoutNaaste($_SESSION["userID"]);
            }
        }
        else
        {
            if(isset($_POST["submitLogin"]))
            {
                $cLogin = new Login($_POST["email"], $_POST["password"]);
                if($cLogin->loginUser())
                {
                    echo $this->display();
                    
                }
                else
                {
                    echo $this->buildLayoutLogin("incorrect");
                }
            }
            else
            {
                echo $this->buildLayoutLogin();
            }
        }
    }
    
    public function buildLayoutTherapist($userID)
    {
        $cLayoutTherapist = new LayoutTherapist($userID);
        
        switch($this->page)
        {
            case "nieuw":
                return $cLayoutTherapist->getNewPage($this->subpage, $this->subsubpage, $this->subsubsubpage);
            case "overzicht":
                return $cLayoutTherapist->getOverviewPage($this->subpage, $this->subsubpage, $this->subsubsubpage, $this->subsubsubsubpage);
            case "home":
            default:
                return $cLayoutTherapist->getHomePage();
        }
    }
    
    public function buildLayoutClient($userID)
    {
        $cLayoutClient = new LayoutClient($userID);
        switch($this->page)
        {
            case 'voortgang':
                return $cLayoutClient->getProgressPage();

            case 'vragenlijsten':
                return $cLayoutClient->getQuestionListPage($this->subpage, $this->subsubpage);

            case "home":
            default:
                return $cLayoutClient->getHomePage();
        }
    }
    
    public function buildLayoutNaaste($userID)
    {
        $cLayoutNaaste = new LayoutNaaste($userID);
        return $cLayoutNaaste->getQuestionListPage($this->subpage, $this->subsubpage);
    }
    
    public function buildLayoutLogin($error = false)
    {
        $cLayoutLogin = new LayoutLogin();
        switch($this->page)
        {
            case "home":
            default:
                return $cLayoutLogin->getLoginPage($error);
        }
    }
}