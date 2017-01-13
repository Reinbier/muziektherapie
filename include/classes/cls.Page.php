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
    
    /**
     * Displays current page based on login session and user role
     */
    public function display()
    {
        if(isset($_SESSION["userID"])) // first check if user already logged in
        {
            $cUser = new User($_SESSION["userID"]);
            
            if($cUser->isTherapist())
            {
                echo $this->buildLayoutTherapist($_SESSION["userID"]); // show therapist specific layout
            }
            else if($cUser->isClient())
            {
                echo $this->buildLayoutClient($_SESSION["userID"]); // show client specific layout
            }
            else
            {
                echo $this->buildLayoutNaaste($_SESSION["userID"]); // show the layout for other roles
            }
        }
        else // show login screen
        {
            if(isset($_POST["submitLogin"]))
            {
                $cLogin = new Login($_POST["email"], $_POST["password"]); // login user
                if($cLogin->loginUser())
                {
                    echo $this->display(); // run this function again for a correct page view
                    
                }
                else
                {
                    echo $this->buildLayoutLogin("incorrect"); // show that the user used false credentials
                }
            }
            else
            {
                echo $this->buildLayoutLogin();
            }
        }
    }
    
    /**
     * Display the layout for the therapist based on the requested page
     * 
     * @param int $userID   The userID
     * @return string       Content
     */
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
    
    
    /**
     * Display the layout for the client based on the requested page
     * 
     * @param int $userID   The userID
     * @return string       Content
     */
    public function buildLayoutClient($userID)
    {
        $cLayoutClient = new LayoutClient($userID);
        switch($this->page)
        {
            case "voortgang":
                return $cLayoutClient->getProgressPage();
            case "vragenlijst":
                return $cLayoutClient->getQuestionListPage();
            case "home":
            default:
                return $cLayoutClient->getHomePage();
        }
    }
    
    
    /**
     * Display the layout for the naaste/professional based on the requested page
     * 
     * @param int $userID   The userID
     * @return string       Content
     */
    public function buildLayoutNaaste($userID)
    {
        $cLayoutNaaste = new LayoutNaaste($userID);
        switch($this->page)
        {
            case "vragenlijst":
                return $cLayoutNaaste->getQuestionListPage();
            case "home":
            default:
                return $cLayoutNaaste->getHomePage();
        }
    }
    
    
    /**
     * Display the layout for the login
     * 
     * @param string|boolean $error     String containing errortype, or false upon no errors
     * @return string                   Content
     */
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