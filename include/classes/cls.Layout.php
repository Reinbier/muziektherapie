<?php

/**
 * @author: Reinier Gombert
 * @date: 5-dec-2016
 */

abstract class Layout extends DAL
{
    
    protected $breadcrumbs;
    
    public function __construct()
    {
        parent::__construct();
        $this->breadcrumbs = array();
    }
    
    /**
     * This method will return the breadcrumb trail
     * 
     * @return string
     */
    protected function getBreadcrumbs()
    {
        $breadcrumbs = '';
        // check if there are any 
        if(count($this->breadcrumbs) > 0)
        {
            $breadcrumbs = '
                <div class="row">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
            ';
            $i = 1;
            $path = "/";
            foreach($this->breadcrumbs as $pageName => $link)
            {
                $path .= $link . "/";
                if($i == count($this->breadcrumbs))
                {
                    $breadcrumbs .= '<li class="active">' . $pageName . '</li>';
                }
                else
                {
                    $breadcrumbs .= '<li><a href="' . $path . '">' . $pageName . '</a></li>';
                }
                $i++;
            }
            $breadcrumbs .= '
                        </ul>
                    </div>
                </div>
            ';
        }
        
        return $breadcrumbs;
    }
    
    public function getHeader()
    {
        return '<img class="img-responsive" src="/images/Weblogo.png" alt="Sonja Aalbers" />';
    }
    
    public function getContent($content)
    {
        return '<div class="container-fluid">' . $content . '</div>';
    }
    
    public function getFooter()
    {
        return '
            <footer class="bd-footer text-muted">
                <div class="container">
                    <p>Project Muziektherapie</p>
                    <p>Copyright 2017 &copy; All rights reserved.</p>
                </div>
            </footer>
        ';
    }
}