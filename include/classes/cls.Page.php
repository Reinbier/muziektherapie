<?php

/**
 * @author: Reinier Gombert
 * @date: 16-nov-2016
 * 
 * Handles the navigation throughout the website, based on the .htaccess
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
    
    /**
     * Displays the page
     */
    public function display()
    {
        if(isset($_SESSION["userID"])) // user is logged in, show his/her page
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
        else // login user if needed
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
            else if(isset($_POST["submitPasswordForgot"])) // user wants to go to passowrd forgotten page
            {
                $cLogin = new Login($_POST["email"], null);
                if($cLogin->checkEmailExists())
                {
                    echo $this->buildLayoutLogin("proceed", $_POST["email"]);
                }
                else
                {
                    echo $this->buildLayoutLogin("incorrect");
                }
            }
            else
            {
                echo $this->buildLayoutLogin(); // just display login page
            }
        }
    }
    
    /**
     * Build the layout for the therapist based on subpages given
     * @param type $userID
     * @return type
     */
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
    
    /**
     * Build the layout for the client based on subpages given
     * @param type $userID
     * @return type
     */
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
    
    /**
     * Build the layout for the kin(Naaste) based on subpages given
     * @param type $userID
     * @return type
     */
    public function buildLayoutNaaste($userID)
    {
        $cLayoutNaaste = new LayoutNaaste($userID);
        return $cLayoutNaaste->getQuestionListPage($this->subpage, $this->subsubpage);
    }
    
    /**
     * Build the layout for the login screen based on errors if given
     * 
     * @param type $error
     * @param type $email
     * @return type
     */
    public function buildLayoutLogin($error = false, $email = null)
    {
        $cLayoutLogin = new LayoutLogin();
        switch($this->page)
        {
            case "forgot-pass":
                return $cLayoutLogin->getForgotPasswordPage($error, $email);
            default:
                return $cLayoutLogin->getLoginPage($error);
        }
    }
}