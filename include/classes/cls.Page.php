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
    
    
}