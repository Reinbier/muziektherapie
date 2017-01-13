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
    
    protected function getBreadcrumbs()
    {
        $breadcrumbs = '';
        if(count($this->breadcrumbs) > 0)
        {
            $breadcrumbs = '
                <div class="row">
                    <div class="col-md-12">
                        <ul class="breadcrumb">
            ';
            $link = '/';
            for($i = 0; $i < count($this->breadcrumbs); $i++)
            {
                $link .= strtolower($this->breadcrumbs[$i]) . '/';
                if($i == (count($this->breadcrumbs) - 1))
                {
                    $breadcrumbs .= '<li class="active">' . $this->breadcrumbs[$i] . '</li>';
                }
                else
                {
                    $breadcrumbs .= '<li><a href="' . $link . '">' . $this->breadcrumbs[$i] . '</a></li>';
                }
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
    }
}