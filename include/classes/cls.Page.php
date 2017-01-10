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
    
    public function display()
    {
        if(isset($_SESSION["userID"]))
        {
            $cUser = new User($_SESSION["userID"]);
            
            if($cUser->isTherapist())
            {
                echo $this->buildLayoutTherapist($_SESSION["userID"]);
            }
            else
            {
                echo $this->buildLayoutClient($_SESSION["userID"]);
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
            case "therapeut":
                return $cLayoutTherapist->getTherapistPage($this->subpage);
            case "client":
                return $cLayoutTherapist->getClientPage($this->subpage);
            case "vragenlijst":
                return $cLayoutTherapist->getQuestionListPage($this->subpage);
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
                break;

            case 'vragenlijst':
                return $cLayoutClient->getQuestionListPage();
                break;

            case "home":
            default:
                return $cLayoutClient->getHomePage();
        }
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